<script setup>
import {ref,computed} from "vue";
import store from "../store";

const test=computed(()=>store.state.test)
defineProps({
  msg: {
    type: String,
    required: true
  }
})
</script>

<template>
  {{test}}
</template>

<style scoped>
h1 {
  font-weight: 500;
  font-size: 2.6rem;
  top: -10px;
}

h3 {
  font-size: 1.2rem;
}

.greetings h1,
.greetings h3 {
  text-align: center;
}

@media (min-width: 1024px) {
  .greetings h1,
  .greetings h3 {
    text-align: left;
  }
}
</style>
