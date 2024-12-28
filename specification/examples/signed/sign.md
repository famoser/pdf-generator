# How to sign a PDF

options:
- not supported by pdftk, qpdf
- open-pdf-sign badly-written wrapper around Java PDFBox
- pdfsig generates invalid certificates, complex operations
- adobe acrobat feature hard to find ("use certificate"), touches/destroys file structure

## pdfsig

generate certificate:
- gpgsm --generate-key (RSA, subject name CN=Florian Moser, self-signed yes)
- gpgsm --import, then paste the certificate followed by CTRL+D
- gpgsm --fingerprint, the copy ID field (e.g. `0x9C1A23DA`)

apply certificate:
- pdfsig signature.pdf signature_signed.pdf -add-signature -backend GPG -nick 0x9C1A23DA (but observe errors in output)
- pdfsig signature_sodapdf.pdf signature_sodapdf_signed.pdf -add-signature -backend GPG -nick 0x9C1A23DA (but resulting file invalid)
- observe that signature of both files are invalid

