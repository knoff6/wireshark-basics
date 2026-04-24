# Assets Directory

Place the following files here for students to download:

## Required Files

1. **Exercise.pcapng** - Main exercise file for answering questions
2. **http1.pcapng** - Demo file for following tutorial screenshots

## Where to Get Files

### Option 1: TryHackMe (Requires Subscription)
- Access the Wireshark: The Basics room
- Download from the provided VM

### Option 2: Create Your Own
The Exercise.pcapng file should contain:
- HTTP traffic
- At least 58,620 packets
- Specific packets with:
  - XML content (packet 38)
  - Artist information (packet 33790)
  - Packet comments (packet 12)
  - A .txt file with "PACKETMASTER"
  - A JPEG image (packet 39765)

### Option 3: Wireshark Sample Captures
Download sample captures from:
- https://wiki.wireshark.org/SampleCaptures

Note: If using different captures, update the answers in `includes/config.php`

## Adding Screenshots (Optional)

Place any tutorial screenshots here with descriptive names:
- wireshark-gui.png
- packet-details.png
- capture-properties.png
- etc.

Then reference them in the task content HTML.
