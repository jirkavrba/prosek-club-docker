import normalizeUrl from "normalize-url";
import {z} from "zod";
import {join} from "node:path";
import {tmpdir} from "node:os";
import sharp from "sharp";
import {decode, encode} from "blurhash";

const RESIZED_IMAGE_SIZE = 250;
const COMPONENTS_X = 4;
const COMPONENTS_Y = 4;

const hashRequestSchema = z.object({
    image_url: z.string().nonempty(),
});

type Result = {
    blurhash: string;
    blurDataUrl: string;
}

let cache: Record<string, Result> = {};

const hashUrl = (input: string): string => {
    return new Bun.CryptoHasher("sha256").update(input).digest("hex");
};

const fetchImageToTempImage = async (
    url: string,
    hash: string,
): Promise<string> => {
    const response = await fetch(url);
    const buffer = await response.arrayBuffer();
    const file = join(tmpdir(), hash);

    await Bun.write(file, buffer);

    return file;
};

const server = Bun.serve({
    routes: {
        "/api/hash": {
            POST: async (request) => {
                const body = await request.json();
                const validated = await hashRequestSchema.safeParseAsync(body);

                if (!validated.success) {
                    return Response.json(
                        {
                            error: "Expected image_url parameter.",
                        },
                        {
                            status: 400,
                        },
                    );
                }

                const normalizedUrl = normalizeUrl(validated.data.image_url);
                const hash = hashUrl(normalizedUrl);

                if (cache[hash]) {
                    console.log(
                        `Using cached blurhash for image url ${normalizedUrl} with hash ${hash} -> ${cache[hash]}`,
                    );

                    return Response.json({
                        result: cache[hash],
                    });
                }

                try {
                    const sourceFile = await fetchImageToTempImage(normalizedUrl, hash);
                    const image = sharp(sourceFile).resize(
                        RESIZED_IMAGE_SIZE,
                        RESIZED_IMAGE_SIZE,
                        {
                            fit: "contain",
                        },
                    );

                    const {data, info} = await image.ensureAlpha(1).raw().toBuffer({
                        resolveWithObject: true,
                    });

                    const pixels = new Uint8ClampedArray(data);
                    const blurhash = encode(
                        pixels,
                        info.width,
                        info.height,
                        COMPONENTS_X,
                        COMPONENTS_Y,
                    );

                    const decodedPixels = decode(blurhash, 16, 16);
                    const decodedImage = await sharp(Buffer.from(decodedPixels), {
                        raw: {
                            width: 16,
                            height: 16,
                            channels: 4
                        }
                    })
                        .webp()
                        .toBuffer();

                    const blurDataUrl = `data:image/webp;base64,${decodedImage.toString("base64")}`;

                    console.log(
                        `Computed blurhash for image url ${normalizedUrl} with hash ${hash} -> blurhash: ${blurhash}, blurDataUrl: ${blurDataUrl}`,
                    );

                    cache[hash] = {
                        blurhash,
                        blurDataUrl,
                    };

                    return Response.json({
                        result: {
                            blurhash,
                            blurDataUrl
                        },
                    });
                } catch (error) {
                    console.error(
                        "Error downloading image and computing blurhash: ",
                        error,
                    );

                    return Response.json(
                        {
                            error:
                                "There was an error downloading image and computing blurhash.",
                        },
                        {
                            status: 500,
                        },
                    );
                } finally {
                    await Bun.file(join(tmpdir(), hash)).delete();
                }
            },
        },
    },
});

console.log(`Server running at ${server.url}.`);
