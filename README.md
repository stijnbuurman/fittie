# Fittie

## What is it?
The goal of this project is to create a centralized place to view all your life tracking metrics. 

### Supported Applications
- Garmin (steps, restingHeartRate)
- Withings (weight, fatFreeMass)
- Google Fit (steps, weight)
- ... more coming soon!



## Requirements
- docker
- docker-compose
- node + npm

## How to install

### Generate SSL
```bash
# Linux / OSX
docker run -it -v $(pwd)/nginx/ssl:/export frapsoft/openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /export/key.pem -out /export/cert.pem
# Windows
docker run -it -v %cd%/nginx/ssl:/export frapsoft/openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /export/key.pem -out /export/cert.pem
```

### Setup .env
```
cp ./back-end/src/.env.example ./back-end/src/.env
```

### Setup Google Fit
- Go to https://console.cloud.google.com/apis/credentials
- Create a project
- Create credentials for an oauth client
- Use the type `webApp`
- Use `https://localhost:8223` as origin
- Use `https://localhost:8223/oauth/google/redirect` as redirectUri
- Download the credentials
- Save the file as `./back-end/src/storage/secrets/client_secret.json`

### Setup Withings
- Create an app here: https://account.withings.com/partner/add_oauth2
- Use `https://localhost:8223/oauth/withings/redirect` as redirectUri
- Fill in the credentials in the .env
```dotenv
WITHINGS_CLIENT_ID=
WITHINGS_CLIENT_SECRET=
WITHINGS_REDIRECT_URI=https://localhost:8223/oauth/withings/redirect
```

### Build front-end
```bash
cd ./back-end/src/
npm install
npm run dev

```

### Run
`docker-compose up -d`

And then go to `https://localhost:8223` and register an account!

# KNOWN ISSUES / TODO
- Security is bad (application tokens/passwords are stored in plain text)
- Error handling is bad 
- Add a retry mechanism (Garmin sometimes throws a 500)
- Refreshing the Withings token sometimes fails

# Roadmap
- Add tests
- Storing / exporting datasets
- Multiple metrics in a single chart
- Create an API
- Add all supported metrics from Garmin/Withings/Google Fit
- Add raw datasources from Google Fit
- Add localization
- Simplify the setup
- Create a proper UI