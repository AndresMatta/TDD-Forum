<template>
    <div :id="'reply-' + id" class="card" :class="isBest ? 'border-success' : ''">
        <div class="card-header" :class="isBest ? 'bg-success text-white' : ''">
            <div class="level">
                <p class="flex">
                    <a :href="'/profiles/' + reply.owner.name">
                        <strong>{{ reply.owner.name }}</strong>
                    </a> <span v-text="ago"></span>
                </p>
                    <div v-if="signedIn">
                        <favorite :reply="reply"></favorite>
                    </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit="update">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body" required></textarea>
                    </div>

                    <button class="btn btn-primary btn-xs">Update</button>
                    <button type="button" class="btn btn-link btn-xs" @click="editing = false">Cancel</button>
                </form>
            </div>
            <div v-else v-html="body">
            </div>
        </div>
            <div class="card-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
                <div v-if="authorize('owns', reply)">
                    <button class="btn btn-info btn-xs mr-1" @click="editing = true">Edit</button>
                    <button class="btn btn-danger btn-xs mr-1" @click="destroy">Delete</button>
                </div>
                <button class="btn btn-success btn-xs mr-1 ml-a" @click="markBestReply" v-if="authorize('owns', reply.thread)">Best Reply</button>
            </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default{
        props: ['reply'],
        components: { Favorite },
        data () {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest
            }
        },
        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }
        },
        created () {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.id, {
                    body: this.body
                })
                .then(() => {
                    flash('Updated!');
                    this.editing = false;
                })
                .catch((error) => {
                    flash(error.response.data, 'danger');
                });
            },
            destroy() {
                axios.delete('/replies/' + this.id);
                
                this.$emit('deleted', this.id);

                flash('Your reply was deleted!');
            },
            markBestReply() {
                axios.post('/replies/' + this.id + '/best');

                window.events.$emit('best-reply-selected', this.id);
            }
        }
    }
</script>