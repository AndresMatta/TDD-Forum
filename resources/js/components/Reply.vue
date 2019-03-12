<template>
    <div :id="'reply-' + id" class="card">
        <div class="card-header">
            <div class="level">
                <p class="flex">
                    <a :href="'/profiles/' + data.owner.name">
                        {{ data.owner.name }}
                    </a> <span v-text="ago"></span>
                </p>
                    <div v-if="signedIn">
                        <favorite :reply="data"></favorite>
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
            <div class="card-footer level" v-if="canUpdate">
                <button class="btn btn-info btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-xs mr-1" @click="destroy">Delete</button>
            </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default{
        props: ['data'],
        components: { Favorite },
        data () {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            }
        },
        computed: {
            signedIn() {
                return window.App.signedIn;
            },
            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id);
            },
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
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
                axios.delete('/replies/' + this.data.id);
                
                this.$emit('deleted', this.data.id);

                flash('Your reply was deleted!');
            }
        }
    }
</script>