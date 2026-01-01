<template>
  <div :class="['mouse-container', { 'is-hidden': hasScrolled }]">
    <div class="mouse-outline">
      <div class="mouse-wheel"></div>
    </div>
    <span class="scroll-text">Scroll Down</span>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const hasScrolled = ref(false);

// Optional: Hide the icon once the user scrolls more than 50px
const handleScroll = () => {
  hasScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.mouse-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: opacity 0.5s ease, visibility 0.5s;
  cursor: default;
}

.is-hidden {
  opacity: 0;
  visibility: hidden;
}

/* The Mouse Body */
.mouse-outline {
  width: 26px;
  height: 44px;
  border: 2px solid #2c3e50; /* Adjust color to match your theme */
  border-radius: 15px;
  position: relative;
}

/* The Wheel/Dot */
.mouse-wheel {
  width: 4px;
  height: 4px;
  background-color: #2c3e50;
  border-radius: 50%;
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  animation: wheel-move 1.6s ease-out infinite;
}

.scroll-text {
  margin-top: 8px;
  font-family: sans-serif;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #2c3e50;
}

/* Animation Logic */
@keyframes wheel-move {
  0% {
    top: 8px;
    opacity: 0;
  }
  30% {
    opacity: 1;
  }
  100% {
    top: 25px;
    opacity: 0;
  }
}
</style>