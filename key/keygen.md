# How to generate privete/public keypair to use formgenerator secrets

Form the terminal execute:
```sh
# generate the private key
openssl genrsa -out private.pem 2048

# extract the public key
openssl rsa -in private.pem -outform PEM -pubout -out public.pem
```

> **Note:**  
> Take care about that at least the file “private.pem” is saved at a location 
> not accessible to anyone from outside!

For usage, load the keys into string values as follows:

```php
$strPrivateKey = file_get_contents('key/private.pem');
$strPublicKey = file_get_contents('key/public.pem');
```

> **Prerequisites:**  
> 'openssl' must be available to generate the keypair.
