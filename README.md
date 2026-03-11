# 🛒 StyleKart - Multivendor E-commerce System

StyleKart is a robust, production-ready multivendor e-commerce platform built with **Laravel 11**. It features a secure, end-to-end delivery management system with OTP verification.

## 🚀 Key Modules Completed
- **Admin Dashboard:** Vendor management, Category/Subcategory control, and Delivery Person assignment.
- **Vendor Portal:** Product management (Variants, Colors, Inventory).
- **Customer Experience:** Product browsing, Cart system, Wishlist, and Secure Checkout.
- **Delivery System (Latest):** - Real-time order tracking.
    - **Secure OTP Verification:** Delivery persons must verify a 6-digit OTP sent to the customer via Gmail SMTP to complete the delivery.

## 🛠️ Technical R&D & Features
- **State Machine:** Managed complex order transitions (Processing -> Shipped -> Out for Delivery -> Delivered).
- **Security:** Integrated Google App Passwords and SSL/TLS encryption for secure mailing.
- **Database Sync:** Real-time synchronization between `orders` and `order_items` tables using Eloquent Models.
- **VCS:** Professional Git branching and version control.

## ⚙️ Setup Instructions
1. Clone the repo: `git clone <url>`
2. Install dependencies: `composer install` & `npm install`
3. Configure `.env` (Database & SMTP settings).
4. Run Migrations: `php artisan migrate --seed`
5. Start Server (stylekart app): `php artisan serve`
6. Start Server (for auto refresh during styles update): `npm run dev`

---

## 🤝 Acknowledgments & Collaboration
Special thanks to **Jinal Rathod** for providing immense clarity and feedback on each module of StyleKart. 
Her guidance was instrumental in shaping the Stylekart and overall feature architecture.

- **GitHub:** [Jinal Rathod](https://github.com/rathodjinal844)  - **Email:** [rathodjinal844@gmail.com](mailto:rathodjinal844@gmail.com)
