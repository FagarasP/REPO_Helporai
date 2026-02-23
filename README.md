# Helpora Communications MVP (Phase 1)

## Architekturplan (kurz)
1. **Domain-Module `communications`**
   - Conversations (1:1)
   - Messages (text + attachments)
   - Presence (online/offline/away)
   - Call signaling + audit log
2. **Transport-Layer**
   - HTTP APIs für MVP-End-to-end
   - Signaling-Endpunkte mit Event-Namen (`call.offer`, `call.answer`, `call.ice`, `call.hangup`)
   - Für echte Echtzeit-WebSockets: Broadcasting/Socket-Layer im nächsten Schritt anschließen
3. **Authorization**
   - Zentraler `CommunicationAuthorizationService` prüft, ob User über Company/Team/Projekt verbunden sind.
4. **UI (Inertia/Vue)**
   - `/messages` Inbox
   - `/messages/{id}` Chat View
   - `CallOverlay` für WebRTC-Bedienelemente

## Task Breakdown
- [x] DB-Schema + Models
- [x] Conversation APIs (`createConversation`, `listConversations`, `sendMessage`, `listMessages`, `markRead`)
- [x] Signaling APIs (`call.offer`, `call.answer`, `call.ice`, `call.hangup`) + Call-Audit-Logs
- [x] Presence APIs (`heartbeat`, `bulk`)
- [x] Frontend Inbox + Chat + Call Overlay
- [x] Unit/Feature Test Skeleton
- [x] ENV Doku

## Neue ENV Variablen
```env
CHAT_MAX_ATTACHMENT_MB=10
CHAT_ATTACHMENT_DISK=public
TURN_URL=turn:your-turn-host:3478
TURN_USER=turn-username
TURN_PASS=turn-password
```

## Lokale Entwicklung
```bash
composer install
npm install
php artisan migrate
npm run dev
php artisan serve
```

### Smoke Test (Chat)
1. Login mit User A und User B in zwei Browsern.
2. Öffne `/messages`, starte Chat, sende Nachricht.
3. Öffne denselben Chat im zweiten Browser, prüfe Anzeige + Mark Read.

## Sicherheit
- Serverseitige Teilnehmerprüfung vor Lesen/Senden/Signaling.
- Attachment-Upload mit Size-Limit.
- Call-Events werden als Audit-Logs gespeichert.
- Presence speichert nur minimalen Status + last seen.
