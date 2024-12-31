#!/usr/bin/env python3

"""This is the docker manager module.

This module shutdown docker container.
"""

__version__ = "0.1"
__author__ = "ZJKiza"

import click
import sys
from pathlib import Path


@click.command()
@click.option("--verbose/--no-verbose", default=False, help="Default is not verbose.", type=bool)
def down(verbose: bool) -> None:
    sys.path.append(str(Path(__file__).resolve().parent.parent))
    from bin.manager.docker_manager import DockerManager

    docker_manager = DockerManager.create(verbose=verbose)
    docker_manager.down_container()


if __name__ == "__main__":
    down()
