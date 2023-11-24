<template>
    <div class="chat-bot container">
        <ul class="collection with-header chat-display">
            <li class="collection-header"><h4>Product Helper Bot</h4></li>
            <li
                class="collection-item"
                v-for="(message, index) in conversation"
                :key="index"
            >
                <span
                    :class="{
                        'user-message': message.sender === 'user',
                        'bot-message': message.sender === 'bot',
                    }"
                >
                    {{ message.text }}
                </span>
            </li>
        </ul>
        <div class="input-field">
            <input
                id="user_query"
                type="text"
                v-model="userInput"
                @keyup.enter="sendQuery"
            />
            <label for="user_query">Ask me about products...</label>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const userInput = ref("");
const conversation = ref([]);

const sendQuery = async () => {
    const userQuery = userInput.value;
    conversation.value.push({ text: userQuery, sender: "user" });
    userInput.value = "";

    // Dummy Axios request (replace with actual API endpoint later)
    try {
        const response = await axios.post('/api/chatbot', { query: userQuery });
        conversation.value.push({ text: response.data.reply, sender: "bot" });
    } catch (error) {
        console.error("API request failed:", error);
        conversation.value.push({
            text: "Sorry, I could not process your request.",
            sender: "bot",
        });
    }
};
</script>

<style scoped>
.chat-bot {
    max-width: 600px;
    margin: 20px auto;
}
.chat-display {
    height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
}
.user-message {
    text-align: right;
    font-weight: bold;
}
.bot-message {
    text-align: left;
    font-style: italic;
}
</style>
