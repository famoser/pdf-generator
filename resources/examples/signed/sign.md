# How to sign a PDF

generate certificate:
- gpgsm --generate-key (RSA, subject name CN=Florian Moser, self-signed yes)
- gpgsm --import, then paste the certificate followed by CTRL+D
- gpgsm --fingerprint, the copy ID field (e.g. `0x9C1A23DA`)

apply certificate:
- pdfsig signature.pdf signature_signed.pdf -add-signature -backend GPG -nick 0x9C1A23DA

review of other tools:
- not supported by pdftk, qpdf
- open-pdf-sign bad wrapper around Java PDFBox