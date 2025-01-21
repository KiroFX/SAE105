class MotusGame {
    constructor() {
        this.currentWord = '';
        this.attempts = 0;
        this.maxAttempts = 6;
        this.currentPlayer = 1;
        this.timeLeft = 30;
        this.timer = null;
        this.revealedIndices = new Set(); // Pour suivre les indices des lettres déjà révélées
        this.currentInput = ''; // Saisie actuelle de l'utilisateur

        this.gameGrid = document.querySelector('.game-grid');
        this.wordInput = document.querySelector('#word-input');

        this.initialize();
    }

    async initialize() {
        await this.fetchNewWord();
        this.createGrid();
        this.setupEventListeners();
        this.revealFirstLetter();
        this.startTimer();
    }

    async fetchNewWord() {
        try {
            const response = await fetch('api/get-word.php');
            const data = await response.json();
            this.currentWord = data.word.toUpperCase();
        } catch (error) {
            console.error('Erreur lors de la récupération du mot:', error);
        }
    }

    createGrid() {
        for (let i = 0; i < this.maxAttempts; i++) {
            const row = document.createElement('div');
            row.className = 'word-row';
            for (let j = 0; j < this.currentWord.length; j++) {
                const letterBox = document.createElement('div');
                letterBox.className = 'letter-box';
                row.appendChild(letterBox);
            }
            this.gameGrid.appendChild(row);
        }
    }

    revealFirstLetter() {
        const firstIndex = 0;
        this.revealedIndices.add(firstIndex);
        for (let row of this.gameGrid.children) {
            const firstBox = row.children[firstIndex];
            firstBox.textContent = this.currentWord[firstIndex];
            firstBox.classList.add('correct');
        }
    }

    setupEventListeners() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                this.submitGuess();
            } else if (e.key === 'Backspace') {
                this.removeLastLetter();
            } else if (e.key.match(/^[a-zA-Z]$/)) {
                this.addLetter(e.key.toUpperCase());
            }
        });

        this.wordInput.style.display = 'none'; // Désactiver l'input texte standard
    }

    addLetter(letter) {
        if (this.currentInput.length < this.currentWord.length) {
            this.currentInput += letter;
            this.updateCurrentRow();
        }
    }

    removeLastLetter() {
        if (this.currentInput.length > 0) {
            this.currentInput = this.currentInput.slice(0, -1);
            this.updateCurrentRow();
        }
    }

    updateCurrentRow() {
        const currentRow = this.gameGrid.children[this.attempts];
        const boxes = currentRow.children;

        for (let i = 0; i < this.currentWord.length; i++) {
            if (this.revealedIndices.has(i)) {
                boxes[i].textContent = this.currentWord[i];
                boxes[i].classList.add('revealed');
            } else if (i < this.currentInput.length) {
                boxes[i].textContent = this.currentInput[i];
                boxes[i].classList.remove('revealed');
            } else {
                boxes[i].textContent = '';
                boxes[i].classList.remove('revealed');
            }
        }
    }

    revealRandomLetter() {
        const unrevealedIndices = [];
        for (let i = 0; i < this.currentWord.length; i++) {
            if (!this.revealedIndices.has(i)) {
                unrevealedIndices.push(i);
            }
        }

        if (unrevealedIndices.length > 0) {
            const randomIndex = unrevealedIndices[Math.floor(Math.random() * unrevealedIndices.length)];
            this.revealedIndices.add(randomIndex);

            for (let row of this.gameGrid.children) {
                const box = row.children[randomIndex];
                box.textContent = this.currentWord[randomIndex];
                box.classList.add('revealed');
            }
        }
    }

    startTimer() {
        this.timer = setInterval(() => {
            this.timeLeft--;
            document.querySelector('.timer').textContent = this.timeLeft;

            if (this.timeLeft <= 0) {
                this.handleTimeout();
            }
        }, 1000);
    }

    resetTimer() {
        clearInterval(this.timer);
        this.timeLeft = 30;
        document.querySelector('.timer').textContent = this.timeLeft;
        this.startTimer();
    }

    handleTimeout() {
        clearInterval(this.timer);

        const currentRow = this.gameGrid.children[this.attempts];
        const boxes = currentRow.children;

        for (let i = 0; i < this.currentWord.length; i++) {
            if (!this.revealedIndices.has(i)) {
                boxes[i].textContent = '-';
                boxes[i].classList.add('incorrect');
            }
        }

        this.attempts++;
        this.currentInput = '';

        this.revealRandomLetter();

        if (this.attempts >= this.maxAttempts) {
            this.handleLoss();
        } else {
            this.timeLeft = 30;
            this.startTimer();
        }
    }

    async submitGuess() {
        if (this.currentInput.length !== this.currentWord.length) {
            return;
        }

        const isValid = await this.checkWordValidity(this.currentInput);
        if (!isValid) {
            alert('Ce mot n\'existe pas dans la liste');
            return;
        }

        this.validateCurrentRow();
        this.attempts++;

        if (this.currentInput === this.currentWord) {
            this.handleWin();
        } else {
            this.revealRandomLetter();
            if (this.attempts >= this.maxAttempts) {
                this.handleLoss();
            } else {
                this.resetTimer();
            }
        }

        this.currentInput = '';
    }

    async checkWordValidity(word) {
        try {
            const response = await fetch('api/check-word.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ word: word })
            });
            const data = await response.json();
            return data.valid;
        } catch (error) {
            console.error('Erreur lors de la vérification du mot:', error);
            return false;
        }
    }

    validateCurrentRow() {
        const row = this.gameGrid.children[this.attempts];
        const boxes = row.children;

        for (let i = 0; i < this.currentInput.length; i++) {
            const box = boxes[i];
            const letter = this.currentInput[i];

            if (letter === this.currentWord[i]) {
                box.classList.add('correct');
            } else if (this.currentWord.includes(letter)) {
                box.classList.add('wrong-position');
            } else {
                box.classList.add('incorrect');
            }
        }
    }

    handleWin() {
        clearInterval(this.timer);
        alert(`Bravo ! Vous avez gagné ! Le mot était : ${this.currentWord}`);
    }

    handleLoss() {
        clearInterval(this.timer);
        alert(`Perdu ! Le mot était : ${this.currentWord}`);
    }
}

// Démarrer le jeu
document.addEventListener('DOMContentLoaded', () => {
    new MotusGame();
});
