terraform {
  required_providers {
    tls = {
      source  = "hashicorp/tls"
      version = "~> 4.0"
    }

    local = {
      source  = "hashicorp/local"
      version = "~> 2.5"
    }

    hcloud = {
      source  = "hetznercloud/hcloud"
      version = "~> 1.62"
    }
  }
}

variable "hcloud_token" {
  sensitive = true
}

provider "hcloud" {
  token = var.hcloud_token
}

resource "tls_private_key" "ssh" {
  algorithm = "ED25519"
}

resource "local_file" "ssh_public_key" {
  content  = tls_private_key.ssh.public_key_openssh
  filename = "${path.module}/hetzner-ssh-key.pub"
}

resource "local_file" "ssh_private_key" {
  content         = tls_private_key.ssh.private_key_openssh
  filename        = "${path.module}/hetzner-ssh-key"
  file_permission = "0600"
}

resource "hcloud_ssh_key" "docker_workshop" {
  name       = "docker-workshop-key"
  public_key = tls_private_key.ssh.public_key_openssh
}

resource "hcloud_server" "docker_workshop" {
  name        = "docker-workshop"
  image       = "debian-13"
  server_type = "cx23"
  location    = "fsn1"
  ssh_keys = [
    hcloud_ssh_key.docker_workshop.id
  ]

  public_net {
    ipv4_enabled = true
    ipv6_enabled = false
  }

}

output "public_ip" {
  value = hcloud_server.docker_workshop.ipv4_address
}
