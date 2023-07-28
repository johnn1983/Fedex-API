<template>
  <ui-kit-modal
    :with-footer="false"
    title="Links"
    ref="linksModal"
    class="links-modal"
  >
    <template v-slot:content>
      <div
        v-if="$props.links"
        class="ui-kit-modal-content"
      >
        <div class="links-modal-item" v-if="permanentLinks.instagram">
          <instagram-icon class="social-icon" />
          <a :href="permanentLinks.instagram" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.instagram }}
          </a>
        </div>

        <div class="links-modal-item" v-if="permanentLinks.facebook">
          <facebook-icon class="social-icon" />
          <a :href="permanentLinks.facebook" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.facebook }}
          </a>
        </div>

        <div class="links-modal-item" v-if="permanentLinks.bandcamp">
          <bandcamp-icon class="social-icon" />
          <a href="" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.bandcamp }}
          </a>
        </div>

        <div class="links-modal-item" v-if="permanentLinks.youtube">
          <youtube-icon class="social-icon" />
          <a :href="permanentLinks.youtube" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.youtube }}
          </a>
        </div>

        <div class="links-modal-item" v-if="permanentLinks.twitch">
          <twitch-icon class="social-icon" />
          <a :href="permanentLinks.twitch" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.twitch }}
          </a>
        </div>

        <div class="links-modal-item" v-if="permanentLinks.patreon">
          <patreon-icon class="social-icon" />
          <a :href="permanentLinks.patreon" class="ui-kit-modal-content-links" target="_blank">
            {{ permanentLinks.patreon }}
          </a>
        </div>

        <div
          v-if="otherLinks.length !== 0"
          v-for="link in otherLinks"
          class="links-modal-item"
        >
          <browser-icon class="social-icon"/>
          <a :href="link" class="ui-kit-modal-content-links">
            {{ link }}
          </a>
        </div>
      </div>

      <div class="links-modal-message" v-else>
        Unfortunately the author did not provide any links.
      </div>

      <div class="ui-kit-modal-content-buttons">
        <button class="button full-width" @click="linksModal.close()">CLOSE</button>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { reactive, onBeforeMount, computed, ref } from 'vue'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import BrowserIcon from '~/assets/svg/social/browser.svg'
import InstagramIcon from '~/assets/svg/social/instagram.svg'
import FacebookIcon from '~/assets/svg/social/facebook.svg'
import BandcampIcon from '~/assets/svg/social/bandcamp.svg'
import YoutubeIcon from '~/assets/svg/social/youtube.svg'
import TwitchIcon from '~/assets/svg/social/twitch.svg'
import PatreonIcon from '~/assets/svg/social/patreon.svg'

interface Props {
  links: string | null
}

const props = defineProps<Props>()
const linksModal = ref<InstanceType<typeof UiKitModal>>(null)
const sites = ref(['twitch', 'youtube', 'patreon'])
const permanentLinks = reactive({
  twitch: null,
  youtube: null,
  patreon: null,
  facebook: null,
  instagram: null,
  bandcamp: null
})
const links = computed(() => props.links)
const otherLinks = ref([])

onBeforeMount(() => {
  if (links.value) {
    siteSort()
  }
})

function siteSort() {

  links.value.split(',').forEach(link => {
    let found = false
    sites.value.forEach(site => {
      const pattern = new RegExp(`(https://)?(www\.)?${site}`, 'i')
      if (pattern.test(link)) {
        console.log(permanentLinks.twitch)
        permanentLinks[site] = link
        found = true
      }
    })

    if (!found) {
      otherLinks.value.push(link)
    }
  })
}

function open() {
  linksModal.value?.open()
}

function close() {
  linksModal.value?.close()
}

defineExpose({ open, close })
</script>