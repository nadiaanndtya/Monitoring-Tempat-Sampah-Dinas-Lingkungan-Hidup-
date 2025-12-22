<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Firebase</title>
</head>
<body>

    <button> Login </button>

    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.3.0/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.3.0/firebase-analytics.js";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
            apiKey: "AIzaSyDnXwpAU-Gu9V4qgCXxkRc2xbVGr_YTt4Q",
            authDomain: "monitoring-project-c6b65.firebaseapp.com",
            projectId: "monitoring-project-c6b65",
            storageBucket: "monitoring-project-c6b65.firebasestorage.app",
            messagingSenderId: "429720588066",
            appId: "1:429720588066:web:629862a23c8e93663d5f7b",
            measurementId: "G-CBJX50QQRY"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
</script>
</body>
</html>