<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';
    import moment from 'moment';

    export default {
        props: ['thread'],
        components: { Replies, SubscribeButton },
        data () {
            return {
                repliesCount: this.thread.replies_count,
                locked_at: this.thread.locked_at
            };
        },
        computed: {
            icon () {
                return this.locked_at ? 'fas fa-lock-open' : 'fas fa-lock';
            },
            state () {
                return this.locked_at ? 'locked.' : 'unlocked.';
            }
        },
        methods: {
            toggleLock () {
                axios.patch('/locked-threads/' +  this.thread.slug).then(() => {
                    this.locked_at = this.locked_at ? null : moment().format();
                    flash('The thread has been ' + this.state)
                });
            }
        }
    }
</script>
