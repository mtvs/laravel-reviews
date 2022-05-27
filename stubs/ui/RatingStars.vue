<template>
    <span class="rating-stars">
       <span v-for="i in 5" 
            @click="$emit('score-selected', i)">
           <i class="icon-star" v-if="i <= score"></i>

            <i class="icon-star-empty" v-else></i>
       </span>
    </span>
</template>

<script>
export default {
    props: [
        'score',
    ]
}
</script>