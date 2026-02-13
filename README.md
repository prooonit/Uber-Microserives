<p align="center">
  <a href="#" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">🚕 Distributed Ride Matching System</h1>

<p align="center">
Laravel API Gateway + Node.js Matching Engine + Redis + BullMQ
</p>

---

## 📌 Project Overview

This project implements a **distributed ride-hailing backend system** inspired by real-world platforms like Uber.

The architecture follows a **microservice-based design**:

- **Laravel** → API Gateway & Authentication Layer  
- **Node.js** → Ride Matching & Lifecycle Engine  
- **Redis** → State management, GEO indexing, distributed locking  
- **BullMQ** → Background wave-based matching  
- **Web Push** → Real-time driver notifications  


High Level Diagram:

![uber archi](https://github.com/user-attachments/assets/bd2591ab-6e04-48d0-83ff-32ea161cdf3f)



---

# 🔹 Laravel Service (API Gateway)

Laravel acts as the centralized entry point for all client requests.

### Responsibilities:

- JWT Authentication
- Role-Based Access Control (RBAC)
- Permission enforcement
- Request validation
- Routing requests to Node microservice
- User & Driver management

All ride-related APIs pass through Laravel before reaching the Node service.

This design follows the **API Gateway Pattern** commonly used in scalable microservice systems.

---

# 🔹 Node.js Matching Engine

The Node service handles:

- Ride request processing
- Geo-based driver discovery (Redis GEO)
- Ride state transitions
- Distributed locking using `SET NX EX`
- Wave-based matching using BullMQ
- Push notification triggering
- Ride lifecycle management

---

# ⚙ Core Features

## 🚗 Ride Lifecycle Management

| Action | From State | To State |
|--------|------------|----------|
| Request Ride | - | PENDING |
| Accept Ride | PENDING | ASSIGNED |
| Start Ride | ASSIGNED | ONGOING |
| Complete Ride | ONGOING | COMPLETED |
| Cancel Ride | PENDING / ASSIGNED | CANCELLED |

---

## 📍 Driver Location System

- Driver location polling every 15 seconds
- Redis GEO indexing for spatial queries
- Heartbeat filtering
- Busy state protection
- Prevents double booking

---

## 🔐 Distributed Locking

Atomic ride assignment:

Ensures only one driver can accept a ride.

---

## 🔄 Wave-Based Matching

When a ride is requested:

1. Nearby drivers are discovered using Redis GEO.
2. Drivers are pushed into a queue.
3. BullMQ worker processes drivers in waves (e.g., 3 per wave).
4. Push notifications are sent.
5. If no driver accepts, next wave triggers.

This prevents notification spam and ensures fair distribution.

---

# 🧠 Redis Key Strategy

ride:{rideId}:status
ride:{rideId}:assigned
ride:{rideId}:user
ride:{rideId}:queue
ride:{rideId}:lock
user:activeRide:{userId}
driver:busy:{driverId}
driver:heartbeat:{driverId}


Goals:

- Fast lookups
- Stateless services
- TTL-based cleanup for temporary states
- Concurrency-safe transitions

---

# 🚀 Running Locally


```bash
### Run Redis
redis-server


### Run Laravel Service

composer install
php artisan serve

### Run Node Location Service- (https://github.com/prooonit/uber-trip-service)

npm install
npm start





