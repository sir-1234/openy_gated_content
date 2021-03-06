<template>
  <div class="videos gated-container">
    <div class="videos__header">
      <h2 class="title">{{ title }}</h2>
      <router-link
        :to="{ name: 'CategoryListing' }"
        v-if="viewAll && listingIsNotEmpty"
        class="view-all"
      >
        View All
      </router-link>
    </div>
    <div v-if="loading" class="text-center">
      <Spinner></Spinner>
    </div>
    <template v-else-if="listingIsNotEmpty">
      <div v-if="error">Error loading</div>
      <div v-else class="video-listing">
        <VideoTeaser
          v-for="video in listing"
          :key="video.id"
          :video="video"
        />
      </div>
    </template>
    <div v-else class="empty-listing">
      Videos not found.
    </div>
  </div>
</template>

<script>
import client from '@/client';
import VideoTeaser from '@/components/video/VideoTeaser.vue';
import Spinner from '@/components/Spinner.vue';
import { JsonApiCombineMixin } from '@/mixins/JsonApiCombineMixin';

export default {
  name: 'VideoListing',
  mixins: [JsonApiCombineMixin],
  components: {
    VideoTeaser,
    Spinner,
  },
  props: {
    title: {
      type: String,
      default: 'Videos',
    },
    excludedVideoId: {
      type: String,
      default: '',
    },
    msg: String,
    category: {
      type: String,
      default: '',
    },
    featured: {
      type: Boolean,
      default: false,
    },
    viewAll: {
      type: Boolean,
      default: false,
    },
    limit: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      loading: true,
      error: false,
      listing: null,
      featuredLocal: false,
      params: [
        'field_gc_video_media',
        'field_gc_video_media.thumbnail',
        'field_gc_video_level',
        'field_gc_video_category',
        'field_gc_video_image',
        'field_gc_video_image.field_media_image',
      ],
    };
  },
  watch: {
    $route: 'load',
    excludedVideoId: 'load',
  },
  async mounted() {
    this.featuredLocal = this.featured;
    await this.load();
  },
  computed: {
    listingIsNotEmpty() {
      return this.listing !== null && this.listing.length > 0;
    },
  },
  methods: {
    async load() {
      this.loading = true;
      const params = {};
      if (this.params) {
        params.include = this.params.join(',');
      }

      params.sort = {
        sortByDate: {
          path: 'created',
          direction: 'DESC',
        },
      };

      params.filter = {};
      if (this.excludedVideoId.length > 0) {
        params.filter.excludeSelf = {
          condition: {
            path: 'id',
            operator: '<>',
            value: this.excludedVideoId,
          },
        };
      }

      if (this.category.length > 0) {
        params.filter['field_gc_video_category.id'] = this.category;
      }

      if (this.featuredLocal) {
        params.filter.field_gc_video_featured = 1;
      }
      params.filter.status = 1;
      if (this.limit !== 0) {
        params.page = {
          limit: this.limit,
        };
      }

      client
        .get('jsonapi/node/gc_video', { params })
        .then((response) => {
          this.listing = this.combineMultiple(
            response.data.data,
            response.data.included,
            this.params,
          );
          if (this.featuredLocal === true && this.listing.length === 0) {
            // Load one more time without featured filter.
            this.featuredLocal = false;
            this.load();
          }
          this.loading = false;
        })
        .catch((error) => {
          this.error = true;
          this.loading = false;
          console.error(error);
          throw error;
        });
    },
  },
};
</script>
