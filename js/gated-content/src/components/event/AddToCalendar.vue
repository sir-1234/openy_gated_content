<template>
  <div class="dropdown add-to-calendar" v-if="allowToShow">
    <button
      class="btn btn-primary dropdown-toggle"
      type="button"
      id="dropdownMenuButton"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false">
      Add to calendar
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a :href="googleLink" target="_blank" class="dropdown-item btn-link">Google</a>
      <a :href="outlookLink" target="_blank" class="dropdown-item btn-link">Outlook</a>
      <a :href="office365Link" target="_blank" class="dropdown-item btn-link">Office 365</a>
      <a :href="yahooLink" target="_blank" class="dropdown-item btn-link">Yahoo</a>
      <a :href="icsLink" target="_blank" class="dropdown-item btn-link">Ics</a>
    </div>
  </div>
</template>

<script>
import {
  google, outlook, office365, yahoo, ics,
} from 'calendar-link';

export default {
  name: 'AddToCalendar',
  props: {
    event: {
      type: Object,
    },
  },
  computed: {
    googleLink() {
      return google(this.event);
    },
    outlookLink() {
      return outlook(this.event);
    },
    office365Link() {
      return office365(this.event);
    },
    yahooLink() {
      return yahoo(this.event);
    },
    icsLink() {
      return ics(this.event);
    },
    allowToShow() {
      return Boolean(this.$store.getters.getAppSettings.event_add_to_calendar);
    },
  },
};
</script>
