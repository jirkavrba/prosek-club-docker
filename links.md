# Docker

- [Docker](https://docker.com)
- [Docker hub](https://hub.docker.com) - repozitář s docker images
- [Docker compose](https://docs.docker.com/compose/) - jednoduchá orchestrace docker containerů
- [Podman](https://podman.io) - alternativa k dockeru, kompatibilní, rootless by default

# GUI Tooling
- [Docker desktop](https://www.docker.com/products/docker-desktop/) - GUI přímo od dockeru, pozor ale! Nedodržuje některé standardy, je placený pro komerční použití
- [Podman desktop](https://podman-desktop.io/) - Jako docker desktop, ale open source a zdarma 
- [Rancher desktop](https://rancherdesktop.io/) - Další open source alternativa Docker desktop, používá se hlavně pro kubernetes.

# CLI Tooling
- [Lazydocker](https://github.com/jesseduffield/lazydocker) - TUI na správu a zobrazení containerů, imagů, logů atd.
- [Dive](https://github.com/wagoodman/dive) - TUI pro koukání do jednotlivých vrstev docker image

# Cool tooly co používají docker
- [Testcontainers](https://testcontainers.com/) - docker containery pro integrační testy
- [Devcontainers](https://containers.dev) - vývojářské prostředí per projekt (otevřu si složku a můžu začít rovnou programovat)
- [Railpack](https://railpack.com) - jak automaticky udělat ze zdrojového kodu docker image bez dockerfile
