<?php
/**
 * DEPRECATED — Legacy PHP Frontend
 *
 * This file is no longer maintained. The production frontend is the Next.js
 * application located at veredas/apps/website/.
 *
 * All users should be redirected to NEXT_PUBLIC_APP_URL via the .htaccess
 * redirect rules. If you are seeing this file, the .htaccess redirect is not
 * active (e.g., running without mod_rewrite). Set APP_FRONTEND_URL in .env.
 *
 * DO NOT add features here — use the Next.js app instead.
 */
$frontendUrl = getenv('APP_FRONTEND_URL') ?: 'http://localhost:3000';
header('Location: ' . $frontendUrl, true, 301);
exit;
