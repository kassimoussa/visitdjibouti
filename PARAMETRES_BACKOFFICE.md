# Paramètres Back-Office - Visit Djibouti

## 📱 **Paramètres Application Mobile**

### **Configuration App**
- **Nom de l'application** : "Visit Djibouti"
- **Version actuelle** : ex: 2.1.5
- **Version minimum supportée** : Forcer les mises à jour
- **Store URLs** : Liens App Store / Google Play
- **Deep Links** : Configuration des liens profonds
- **Bundle ID** : com.djibouti.visit

### **API & Synchronisation**
- **URL de l'API** : Point d'accès pour l'app mobile
- **Clés API** : Google Maps, météo, traduction
- **Fréquence de sync** : Toutes les 6h, 24h, etc.
- **Données hors-ligne** : POI essentiels à télécharger
- **Cache mobile** : Durée de rétention des données

### **Notifications Push**
- **Firebase/FCM** : Configuration serveur
- **Types de notifications** : Nouveaux POI, événements, alertes météo
- **Segmentation** : Par région, langue, centres d'intérêt
- **Programmation** : Notifications automatiques

### **Géolocalisation & Cartes**
- **Précision GPS** : Rayon d'acceptation (ex: 100m)
- **Cartes hors-ligne** : Zones téléchargeables
- **Points d'intérêt prioritaires** : Affichage par défaut
- **Rayon de recherche** : 1km, 5km, 10km, etc.

### **Contenu & Modération**
- **Commentaires/Avis** : Modération automatique/manuelle
- **Signalements** : Seuil avant masquage automatique
- **Photos utilisateurs** : Validation avant publication
- **Langues** : Ordre de priorité FR > EN > AR

## 🔧 **Onglets supplémentaires**

### **📊 Analytics & Stats**
- **Utilisateurs actifs** : Par jour/semaine/mois
- **POI les plus visités**
- **Parcours utilisateurs** : Heat map des déplacements
- **Téléchargements** : Stats App Store
- **Crash reports** : Erreurs app mobile

### **🚨 Alertes & Monitoring**
- **État des services** : API, serveurs, CDN
- **Alertes touristiques** : Zones déconseillées
- **Météo extrême** : Canicule, tempêtes
- **Événements spéciaux** : Fermetures, travaux

### **👥 Gestion Utilisateurs Mobile**
- **Utilisateurs inscrits**
- **Profils bloqués/suspendus** 
- **Préférences utilisateurs**
- **Historique des actions**

### **📦 Gestion Contenu Mobile**
- **Contenu en attente** (reviews, photos)
- **Modération** des avis/commentaires
- **Push notifications** à envoyer
- **Contenu épinglé** (mis en avant dans l'app)

### **🔄 Synchronisation**
- **Dernière sync** avec l'app
- **Données en attente** de sync
- **Conflits de données**
- **Logs de synchronisation**

---

**Note** : Ce fichier contient la planification des fonctionnalités à développer pour le back-office de l'application mobile Visit Djibouti.

**Priorité** : Commencer par "Paramètres Application" car c'est le cœur de la liaison back-office ↔ app mobile.