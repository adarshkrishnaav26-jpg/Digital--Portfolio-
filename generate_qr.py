import sys
import qrcode
import qrcode.image.svg

if len(sys.argv) != 3:
    print("Usage: python generate_qr.py URL OUTPUT_FILE")
    sys.exit(1)

url = sys.argv[1]
output = sys.argv[2]

qr = qrcode.QRCode(
    version=None,
    error_correction=qrcode.constants.ERROR_CORRECT_M,
    box_size=10,
    border=4
)

qr.add_data(url)
qr.make(fit=True)

factory = qrcode.image.svg.SvgPathImage
image = qr.make_image(image_factory=factory)
image.save(output)

print("QR Code generated successfully:", output)
