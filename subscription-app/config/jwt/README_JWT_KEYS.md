# 🔑 Génération des clés JWT

Ce projet utilise **LexikJWTAuthenticationBundle** pour l’authentification via JWT.  
Vous devez générer un couple de clés publique/privée avant de pouvoir utiliser l’API.

---

## 📌 Script de génération

Ajoutez ce script dans votre README pour générer vos clés facilement :

```bash
#!/usr/bin/env bash
set -e

# Répertoire des clés
KEY_DIR="config/jwt"
PASSPHRASE=${JWT_PASSPHRASE:-"changeit"}

# Nettoyage ancien jeu de clés
rm -rf config/jwt  
mkdir -p config/jwt  

# Génération de la clé privée protégée par la passphrase
openssl genrsa -out config/jwt/private.pem -aes256 4096

# Génération de la clé publique
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Droits en lecture
chmod 644 config/jwt/*.pem
```

---

## 📂 Résultat attendu

Les clés générées se trouvent dans le dossier `config/jwt/` :

- `private.pem` → clé privée utilisée pour signer les tokens  
- `public.pem` → clé publique utilisée pour vérifier les tokens  

---

## ⚠️ Configuration

Ajoutez dans votre fichier `.env.local` :

```dotenv
JWT_PASSPHRASE=ma_passphrase
```

La valeur doit correspondre **exactement** à la passphrase utilisée lors de la génération des clés.
