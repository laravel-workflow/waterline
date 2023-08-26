#!/usr/bin/env bash

/opt/mssql-tools/bin/sqlcmd -S "mssql" -U "sa" -P "P@ssword" -Q "CREATE DATABASE testing"
