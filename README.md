# phpipam-subnet-visual
Enhanced PHPIPAM Subnet Visualization

# Tested on:
subnet masks: /22 -> /30

phpipam version: 1.4.4

# Notes:
I just tested this with the data I had available.
Some of the block headings might be off alignment, just started writing PHP and just need to find the functions for ensuring proper spacing.

e.g., start and stop ranges >= 29 characters total. xxx.xxx.xxx.xxx - xxx.xxx.xxx.xxx

# Replaces:

../phpipam/app/subnets/subnet-visual.php

../phpipam/css/bootstrap/bootstrap-custom-dark.css

# Bootstrap CSS
Adds in CSS for Reserved IP Addresses (ip-3) so Reserved IP Address appear differently than Used/Unused/Disabled

.ip_vis span.ip-3{background:rgba(0,0,255,0.05) !important;color:white !important}

# Features - Example
Legend Blocks [IP]: Blue (Reserved), Green (Used), Light Gray (Unused), Dark Gray (Disabled Out-of-Range)

Block Headers are: "Starting Usable Address - End Usable Address"

Removed the "." in ".IP" for all blocks, better visual aesthetics and centering for 0-255 in spans.

![example](https://user-images.githubusercontent.com/5930058/130671456-d85acd10-bb7f-4927-ab59-4ce9319aedfe.png)


# /22 Subnet Example
![example-22](https://user-images.githubusercontent.com/5930058/130673057-35d3f570-91f3-481f-bf3f-9443a43d74a1.png)

