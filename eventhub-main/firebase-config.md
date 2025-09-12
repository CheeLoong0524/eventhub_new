# 🔥 Firebase Configuration Guide for EventHub

## **📋 Required Environment Variables**

Add these to your `.env` file:

```env
# Firebase Configuration
FIREBASE_CREDENTIALS_FILE=storage/firebase-credentials.json
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_DATABASE_URL=https://your-project.firebaseio.com
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abcdef123456
FIREBASE_API_KEY=your-firebase-api-key
```

## **🚀 Firebase Project Setup**

### **Step 1: Create Firebase Project**
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click "Create a project"
3. Enter project name: `eventhub` (or your preferred name)
4. Enable Google Analytics (optional)
5. Click "Create project"

### **Step 2: Enable Authentication**
1. In Firebase Console, go to "Authentication"
2. Click "Get started"
3. Enable these sign-in methods:
   - **Email/Password** ✅
   - **Google** ✅
   - **Phone** (optional)
   - **Facebook** (optional)

### **Step 3: Get Configuration**
1. Click the gear icon ⚙️ next to "Project Overview"
2. Select "Project settings"
3. Scroll down to "Your apps"
4. Click the web icon (</>)
5. Register app with name: `EventHub Web`
6. Copy the configuration object

### **Step 4: Download Service Account Key**
1. In Project settings, go to "Service accounts"
2. Click "Generate new private key"
3. Download the JSON file
4. Rename to `firebase-credentials.json`
5. Place in `storage/` folder

## **🔧 Laravel Integration**

### **Step 1: Update .env File**
Copy the Firebase config values to your `.env` file.

### **Step 2: Place Credentials File**
Put `firebase-credentials.json` in `storage/` folder.

### **Step 3: Clear Config Cache**
```bash
php artisan config:clear
```

## **✅ Testing Firebase Auth**

1. **Start Laravel server:**
   ```bash
   php artisan serve
   ```

2. **Visit:** http://127.0.0.1:8000/auth/firebase

3. **Test features:**
   - Email/password sign up
   - Email/password sign in
   - Google sign in
   - Password reset (automatic with Firebase)

## **🔒 Security Features**

- ✅ **Email verification** (automatic)
- ✅ **Password strength** (Firebase handles)
- ✅ **Rate limiting** (Firebase handles)
- ✅ **Multi-factor auth** (can be enabled)
- ✅ **Social login** (Google, Facebook, etc.)

## **📱 Mobile & Web Ready**

- ✅ **Progressive Web App** support
- ✅ **Mobile responsive** design
- ✅ **Cross-platform** authentication
- ✅ **Offline support** capabilities

## **🚨 Troubleshooting**

### **Common Issues:**
1. **"Firebase not initialized"** → Check API key in .env
2. **"Credentials file not found"** → Verify file path
3. **"Authentication failed"** → Check Firebase console settings

### **Debug Commands:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## **🎯 Next Steps**

After Firebase setup:
1. Test authentication flow
2. Customize user roles
3. Add more OAuth providers
4. Implement password reset UI
5. Add phone authentication

---

**🎉 Congratulations!** Your EventHub now uses Firebase Authentication for enterprise-grade security and scalability!
