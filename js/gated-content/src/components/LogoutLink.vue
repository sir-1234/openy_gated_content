<template>
  <span class="menu">
    <a @click.prevent="logout" href="#">Sign out</a>
  </span>
</template>

<script>
export default {
  name: 'LogoutLink',
  methods: {
    logout() {
      this.$store.dispatch(`${this.$store.getters.authPlugin}Logout`);
      this.$router.push({ name: 'Login' }).catch(() => {});
    },
  },
};
</script>
