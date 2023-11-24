<template>
    <div class="chat-bot container">
        <h4>Product Helper Bot</h4>
        <ul class="collection with-header chat-display">
            <li
                class="collection-item"
                v-for="(message, index) in conversation"
                :key="index"
            >
                <div v-if="message.isProduct" class="product-details">
                    <div><strong>Product:</strong> {{ message.text.name }}</div>
                    <div>
                        <strong>Description:</strong>
                        {{ message.text.description }}
                    </div>
                    <div><strong>Color:</strong> {{ message.text.color }}</div>
                    <div><strong>Size:</strong> {{ message.text.size }}</div>
                    <div><strong>Price:</strong> {{ message.text.price }}</div>
                </div>
                <div
                    v-else
                    :class="{
                        'user-message': message.sender === 'user',
                        'bot-message': message.sender === 'bot',
                    }"
                >
                    {{ message.text }}
                </div>
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

    try {
        const response = await axios.post("/api/chatbot", { query: userQuery });

        // Add the reply text to the conversation
        conversation.value.push({ text: response.data.reply, sender: "bot" });

        // Add formatted product details to the conversation
        if (response.data.products && response.data.products.length > 0) {
            const productDetails = formatProductDetails(response.data.products);
            productDetails.forEach((detail) => {
                conversation.value.push({
                    text: detail,
                    sender: "bot",
                    isProduct: true,
                });
            });
        } else {
            // No products found - provide a suggestion
            const suggestion = generateSuggestion(userQuery);
            conversation.value.push({ text: suggestion, sender: "bot" });
        }
    } catch (error) {
        console.error("API request failed:", error);
    }
};

const generateSuggestion = (userQuery) => {
    return "Not sure what you're looking for? Try searching for a product category, color, size, or price range.";
};

const formatProductDetails = (products) => {
    return products.map((product) => {
        return {
            name: product.name,
            description: product.description || "No description available",
            color: product.color,
            size: product.size,
            price: product.price,
        };
    });
};
</script>

<style scoped>
.chat-bot {
    max-width: 600px;
    margin: 20px auto;
}
.chat-display {
    height: 1000px;
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
.product-details {
    text-align: left;
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f5f5f5;
}
</style>
