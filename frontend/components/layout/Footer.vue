<template>
  <footer class="bg-gray-900 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Brand -->
        <div>
          <div class="flex items-center space-x-2 mb-4">
            <span class="text-2xl">🐾</span>
            <span class="text-xl font-bold">Pet Listing</span>
          </div>
          <p class="text-gray-400 text-sm">
            Find your perfect pet companion from trusted sellers across the UK.
          </p>
        </div>

        <!-- Browse -->
        <div>
          <h3 class="font-semibold mb-4">Browse</h3>
          <ul class="space-y-2 text-gray-400 text-sm">
            <li>
              <NuxtLink to="/dogs" class="hover:text-white transition">
                Dogs for Sale
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/dogs?listing_type=adoption" class="hover:text-white transition">
                Dogs for Adoption
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/dogs?listing_type=stud" class="hover:text-white transition">
                Stud Dogs
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h3 class="font-semibold mb-4">Support</h3>
          <ul class="space-y-2 text-gray-400 text-sm">
            <li>
              <a href="#" class="hover:text-white transition">Help Center</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition">Safety Tips</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition">Contact Us</a>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="font-semibold mb-4">Legal</h3>
          <ul class="space-y-2 text-gray-400 text-sm">
            <li>
              <a href="#" class="hover:text-white transition">Terms of Service</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition">Privacy Policy</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition">Cookie Policy</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
        <p>&copy; {{ currentYear }} Pet Listing. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const currentYear = new Date().getFullYear()
</script>
