#!/bin/sh
YAPAF_DEV_SERVER=1 php -d variables_order=EGPCS -S localhost:8080 -t demo/public demo/public/index.php

