// vite.config.js
import { defineConfig } from "file:///D:/Dev/projects/alnouran-hesham/node_modules/vite/dist/node/index.js";
import laravel from "file:///D:/Dev/projects/alnouran-hesham/node_modules/laravel-vite-plugin/dist/index.mjs";
import vue from "file:///D:/Dev/projects/alnouran-hesham/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vuetify from "file:///D:/Dev/projects/alnouran-hesham/node_modules/vite-plugin-vuetify/dist/index.js";
var host = "localhost";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/sass/app.scss", "resources/js/main.js"],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    vuetify({ autoImport: true })
  ],
  resolve: {
    alias: {
      vue: "vue/dist/vue.esm-bundler.js"
    }
  },
  server: {
    host,
    hmr: { host }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxEZXZcXFxccHJvamVjdHNcXFxcYWxub3VyYW4taGVzaGFtXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxEZXZcXFxccHJvamVjdHNcXFxcYWxub3VyYW4taGVzaGFtXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9EZXYvcHJvamVjdHMvYWxub3VyYW4taGVzaGFtL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCB2dWV0aWZ5IGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZXRpZnknO1xuY29uc3QgaG9zdCA9ICdsb2NhbGhvc3QnO1xuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiBwbHVnaW5zOiBbXG4gIGxhcmF2ZWwoe1xuICAgaW5wdXQ6IFsncmVzb3VyY2VzL3Nhc3MvYXBwLnNjc3MnLCAncmVzb3VyY2VzL2pzL21haW4uanMnXSxcbiAgIHJlZnJlc2g6IHRydWUsXG4gIH0pLFxuICB2dWUoe1xuICAgdGVtcGxhdGU6IHtcbiAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgYmFzZTogbnVsbCxcbiAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICB9LFxuICAgfSxcbiAgfSksXG4gIHZ1ZXRpZnkoeyBhdXRvSW1wb3J0OiB0cnVlIH0pLFxuIF0sXG4gcmVzb2x2ZToge1xuICBhbGlhczoge1xuICAgdnVlOiAndnVlL2Rpc3QvdnVlLmVzbS1idW5kbGVyLmpzJyxcbiAgfSxcbiB9LFxuIHNlcnZlcjoge1xuICBob3N0LFxuICBobXI6IHsgaG9zdCB9LFxuIH0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBdVIsU0FBUyxvQkFBb0I7QUFDcFQsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUNoQixPQUFPLGFBQWE7QUFDcEIsSUFBTSxPQUFPO0FBQ2IsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDM0IsU0FBUztBQUFBLElBQ1IsUUFBUTtBQUFBLE1BQ1AsT0FBTyxDQUFDLDJCQUEyQixzQkFBc0I7QUFBQSxNQUN6RCxTQUFTO0FBQUEsSUFDVixDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsTUFDSCxVQUFVO0FBQUEsUUFDVCxvQkFBb0I7QUFBQSxVQUNuQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNsQjtBQUFBLE1BQ0Q7QUFBQSxJQUNELENBQUM7QUFBQSxJQUNELFFBQVEsRUFBRSxZQUFZLEtBQUssQ0FBQztBQUFBLEVBQzdCO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDUixPQUFPO0FBQUEsTUFDTixLQUFLO0FBQUEsSUFDTjtBQUFBLEVBQ0Q7QUFBQSxFQUNBLFFBQVE7QUFBQSxJQUNQO0FBQUEsSUFDQSxLQUFLLEVBQUUsS0FBSztBQUFBLEVBQ2I7QUFDRCxDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
