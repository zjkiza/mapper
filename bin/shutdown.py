#!/usr/bin/env python3

import click
from utility.docker_manager_factory import create_docker_manager


@click.command()
@click.option('--verbose/--no-verbose', default=False, help='Default is not verbose.', type=bool)
def down(verbose: bool) -> None:
    docker_manager = create_docker_manager(verbose=verbose)
    docker_manager.down_container()


if __name__ == '__main__':
    down()

