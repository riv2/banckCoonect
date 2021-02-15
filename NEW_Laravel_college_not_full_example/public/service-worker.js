const PRECACHE = 'precache-v1';
const RUNTIME = 'runtime';

const PRECACHE_URLS = [
 "/",
 "/lte_assets/plugins/jquery/jquery.min.js",
 "/lte_assets/plugins/jquery-ui/jquery-ui.min.js",
 "/lte_assets/dist/js/jquery.dataTables.js",
 "/lte_assets/plugins/bootstrap/js/bootstrap.bundle.min.js",

 "/lte_assets/plugins/chart.js/Chart.min.js",
 "/lte_assets/plugins/sparklines/sparkline.js",
 "/lte_assets/plugins/jqvmap/jquery.vmap.min.js",
 "/lte_assets/plugins/jqvmap/maps/jquery.vmap.world.js",
 "/lte_assets/plugins/jquery-knob/jquery.knob.min.js",
 "/lte_assets/plugins/moment/moment.min.js",
 "/manifest.json",

 "/assets/js/app.js",

 "/lte_assets/plugins/daterangepicker/daterangepicker.js",
 "/lte_assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js",
 "/lte_assets/plugins/summernote/summernote-bs4.min.js",
 "/lte_assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js",
 "/lte_assets/dist/js/adminlte.js",
 "/lte_assets/dist/js/uikit.min.js",
 "/lte_assets/dist/js/uikit-icons.min.js",
 "/lte_assets/dist/js/script.js",
 "/lte_assets/dist/js/analytics.js?id=UA-7924497-32",

 "/assets/js/application.js?v=1",
 "/assets/js/profile.js",
 "/assets/js/vue.js",
 "/admin_assets/js/axios.min.js",
 "/service-worker.js",
 "/assets/js/bootstrap-vue.js",

 "/bvi.isvek/js/responsivevoice.min.js",
 "/bvi.isvek/js/js.cookie.js",
 "/bvi.isvek/js/bvi-init.js",
 "/bvi.isvek/js/bvi.min.js",


 "/bvi.isvek/css/bvi-font.min.css",
 "/bvi.isvek/css/bvi.min.css",
 "/lte_assets/plugins/fontawesome-free/css/all.min.css",
 "/lte_assets/dist/css/ionicons.min.css",
 "/lte_assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css",
 "/lte_assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css",
 "/lte_assets/plugins/jqvmap/jqvmap.min.css",
 "/lte_assets/dist/css/adminlte.min.css",
 "/lte_assets/dist/css/jquery.dataTables.css",
 "/lte_assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css",
 "/lte_assets/plugins/daterangepicker/daterangepicker.css",
 "/lte_assets/plugins/summernote/summernote-bs4.css",
 "lte_assets/dist/css/uikit.min.css"
];




// The install handler takes care of precaching the resources we always need.
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(PRECACHE)
      .then(cache => cache.addAll(PRECACHE_URLS))
      .then(self.skipWaiting())
  );
});

// The activate handler takes care of cleaning up old caches.
self.addEventListener('activate', event => {
  //const currentCaches = [PRECACHE, RUNTIME];
  const currentCaches = [PRECACHE];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return cacheNames.filter(cacheName => !currentCaches.includes(cacheName));
    }).then(cachesToDelete => {
      return Promise.all(cachesToDelete.map(cacheToDelete => {
        return caches.delete(cacheToDelete);
      }));
    }).then(() => self.clients.claim())
  );
});

// The fetch handler serves responses for same-origin resources from a cache.
// If no response is found, it populates the runtime cache with the response
// from the network before returning it to the page.

self.addEventListener('fetch', event => {
  //console.log('event.request.url', event.request.url);
  //console.log('self.location.origin', self.location.origin);
  
  var req = event.request.clone();
  // Skip cross-origin requests, like those for Google Analytics.
  //if (event.request.url == self.location.origin ) {
  if (event.request.url.startsWith(self.location.origin) && 
      event.request.method === 'GET' &&
      !event.request.url.startsWith("admin") && 
      !event.request.url.includes("json") &&
      !event.request.url.includes("login")&&
      !event.request.url.includes("logout") &&
      event.request.url == self.location.origin ) {
    event.respondWith(
      caches.match(event.request).then(cachedResponse => {
        if (cachedResponse) {
          return cachedResponse;
        }

        return caches.open(RUNTIME).then(cache => {
          return fetch(event.request).then(response => {
            // Put a copy of the response in the runtime cache.
            return cache.put(event.request, response.clone()).then(() => {
              return response;
            });
          });
        });
      })
    );
  }
});








