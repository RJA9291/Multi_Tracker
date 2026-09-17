#!/usr/bin/env python3
"""Generate Multi Tracker PWA icons (PNG) — green gradient tile + white check.
Pure Python, no Pillow. Produces icon-180/192/512.png in the project root."""
import zlib, struct, math, os

OUT = "."
TOP = (0x2E, 0xC0, 0x81)   # green top
BOT = (0x11, 0x7A, 0x4C)   # green bottom
MARK = (255, 255, 255)     # checkmark colour

# Checkmark as a polyline in normalized [0,1] coords (short arm, then long arm).
CHECK = [(0.30, 0.53), (0.44, 0.67), (0.72, 0.35)]


def dist_seg(px, py, ax, ay, bx, by):
    dx, dy = bx - ax, by - ay
    L2 = dx * dx + dy * dy
    if L2 == 0:
        return math.hypot(px - ax, py - ay)
    t = max(0.0, min(1.0, ((px - ax) * dx + (py - ay) * dy) / L2))
    return math.hypot(px - (ax + t * dx), py - (ay + t * dy))


def make_icon(size, path):
    W = H = size
    ss = 3
    half = size * 0.062          # half stroke width
    pts = [(x * size, y * size) for (x, y) in CHECK]

    def on_mark(x, y):
        d = min(
            dist_seg(x, y, pts[0][0], pts[0][1], pts[1][0], pts[1][1]),
            dist_seg(x, y, pts[1][0], pts[1][1], pts[2][0], pts[2][1]),
        )
        return d <= half

    raw = bytearray()
    for y in range(H):
        raw.append(0)
        t = y / (H - 1)
        bg = (int(TOP[0] + (BOT[0] - TOP[0]) * t),
              int(TOP[1] + (BOT[1] - TOP[1]) * t),
              int(TOP[2] + (BOT[2] - TOP[2]) * t))
        for x in range(W):
            hits = 0
            for sy in range(ss):
                for sx in range(ss):
                    if on_mark(x + (sx + 0.5) / ss, y + (sy + 0.5) / ss):
                        hits += 1
            a = hits / (ss * ss)
            if a <= 0:
                raw += bytes((bg[0], bg[1], bg[2], 255))
            else:
                raw += bytes((int(bg[0] + (MARK[0] - bg[0]) * a),
                              int(bg[1] + (MARK[1] - bg[1]) * a),
                              int(bg[2] + (MARK[2] - bg[2]) * a), 255))

    def chunk(typ, data):
        return struct.pack(">I", len(data)) + typ + data + struct.pack(">I", zlib.crc32(typ + data) & 0xffffffff)
    png = (b'\x89PNG\r\n\x1a\n'
           + chunk(b'IHDR', struct.pack(">IIBBBBB", W, H, 8, 6, 0, 0, 0))
           + chunk(b'IDAT', zlib.compress(bytes(raw), 9))
           + chunk(b'IEND', b''))
    with open(os.path.join(OUT, path), 'wb') as f:
        f.write(png)
    print(path, len(png), "bytes")


for s, p in [(180, 'icon-180.png'), (192, 'icon-192.png'), (512, 'icon-512.png')]:
    make_icon(s, p)
print("done")
