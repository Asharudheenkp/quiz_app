<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { usePage, Link } from '@inertiajs/vue3';

const { props } = usePage();

const category = props.category;
const questions = ref([]);
const currentQuestionIndex = ref(0);
const timer = ref(30);
const selectedAnswer = ref(null);
const results = ref(null);
const loading = ref(true);

const fetchQuestions = async () => {
    try {
        const response = await axios.get(`https://the-trivia-api.com/api/questions?categories=${category}&limit=15`);
        questions.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error("Failed to fetch questions:", error);
        loading.value = false;
    }
};

const nextQuestion = () => {
    selectedAnswer.value = null;
    if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++;
        timer.value = 30;
    } else {
        calculateResults();
    }
};

const calculateResults = () => {
    const totalQuestions = questions.value.length;
    const correctAnswers = questions.value.filter(
        (question) => question.correctAnswer === question.userAnswer
    ).length;
    const percentage = (correctAnswers / totalQuestions) * 100;

    results.value = {
        details: questions.value.map((question) => ({
            question: question.question,
            correctAnswer: question.correctAnswer,
            selectedAnswer: question.userAnswer || null,
            isCorrect: question.correctAnswer === question.userAnswer,
        })),
        percentage,
    };
};


onMounted(() => {
    fetchQuestions();
    const interval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--;
        } else {
            nextQuestion();
        }
    }, 1000);

    watch(
        () => results.value,
        () => {
            if (results.value) clearInterval(interval);
        }
    );
});

const selectAnswer = (answer) => {
    if (!results.value) {
        questions.value[currentQuestionIndex.value].userAnswer = answer;
        nextQuestion();
    }
};
</script>

<template>

    <AuthenticatedLayout>
        <div>
            <div class="py-12">
                <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div v-if="loading">Loading questions...</div>
                    <div v-else-if="results" class="bg-white p-10 rounded-md">
                        <h2 class="text-3xl font-bold text-center mb-5">Results</h2>
                        <div v-for="(result, index) in results.details" :key="index" class="mb-4">
                            <p><strong>Question {{ index + 1 }}:</strong> {{ result.question }}</p>
                            <p>Your Answer: {{ result.selectedAnswer || "No Answer" }}</p>
                            <p>Correct Answer: {{ result.correctAnswer }}</p>
                            <p :class="{ 'text-green-500': result.isCorrect, 'text-red-500': !result.isCorrect }">
                                {{ result.isCorrect ? "Correct" : "Incorrect" }}
                            </p>
                        </div>

                        <div class="mt-6 text-center">
                            <p v-if="results.percentage > 60"
                                class="mt-3 bg-green-500 text-white inline-block py-2 px-6 rounded text-3xl">
                                Winner
                            </p>
                            <p v-else-if="results.percentage >= 40"
                                class="mt-3 bg-yellow-500 text-white inline-block py-2 px-6 rounded text-3xl">
                                Better
                            </p>
                            <p v-else class="mt-3 bg-red-500 text-white inline-block py-2 px-6 rounded text-3xl">
                                Failed
                            </p>
                        </div>
                    </div>

                    <div v-else class="bg-white p-10 rounded-md">
                        <div class="flex justify-between">
                            <p
                                class="bg-blue-500 text-white flex justify-center items-center rounded-full w-10 h-10 mb-5">
                                {{ currentQuestionIndex + 1 }}</p>
                            <p class="mt-4">Time Remaining: {{ timer }} seconds</p>
                        </div>

                        <h2>{{ questions[currentQuestionIndex].question }}</h2>
                        <div class="grid grid-cols-2 gap-4 mt-4 ">
                            <button
                                v-for="answer in [...questions[currentQuestionIndex].incorrectAnswers, questions[currentQuestionIndex].correctAnswer].sort()"
                                :key="answer" @click="selectAnswer(answer)"
                                class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                                {{ answer }}
                            </button>
                        </div>
                    </div>
                    <div class="text-center">
                        <Link v-if="!loading" :href="route('home')"><span class="bg-slate-600 px-4 py-2 rounded-md text-white">Reset</span></Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
