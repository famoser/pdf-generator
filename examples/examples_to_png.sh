gs -dBATCH -dNOPAUSE -sDEVICE=png16m -r600 -dLastPage=4 -sOutputFile=book_%d.png book.pdf
gs -dBATCH -dNOPAUSE -sDEVICE=png16m -r600 -sOutputFile=invoice.png invoice.pdf
