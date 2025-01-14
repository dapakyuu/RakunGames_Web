from flask import Flask, request, jsonify
from textblob import TextBlob
from googletrans import Translator
import mysql.connector
import json
from flask_cors import CORS
import re
from typing import Dict

app = Flask(__name__)
CORS(app)

@app.route('/', methods=['GET'])
def test():
    return jsonify({"message": "API is working!"})

def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="rakungames"
        )
        print("Database connection successful")
        return conn
    except Exception as e:
        print(f"Database connection error: {str(e)}")
        return None

def load_slang_dict() -> Dict[str, str]:
    """Kamus untuk kata slang/gaul bahasa Indonesia"""
    return {
        'gw': 'saya',
        'gue': 'saya',
        'lu': 'kamu',
        'elo': 'kamu',
        'kyk': 'seperti',
        'kmrn': 'kemarin',
        'bgt': 'banget',
        'sgt': 'sangat',
        'byk': 'banyak',
        'udh': 'sudah',
        'udah': 'sudah',
        'dah': 'sudah',
        'gk': 'tidak',
        'ga': 'tidak',
        'gak': 'tidak',
        'ngga': 'tidak',
        'nggak': 'tidak',
        'tdk': 'tidak',
        'krn': 'karena',
        'karna': 'karena',
        'dgn': 'dengan',
        'yg': 'yang',
        'utk': 'untuk',
        'bwt': 'buat',
        'skrg': 'sekarang',
        'hrs': 'harus',
        'hrus': 'harus',
        'bisa': 'dapat',
        'bs': 'bisa',
        'trs': 'terus',
        'trus': 'terus',
        'tp': 'tapi',
        'tpi': 'tapi',
        'tp': 'tetapi',
        'ok': 'oke',
        'oke': 'oke',
        'thx': 'terima kasih',
        'thanks': 'terima kasih',
        'makasih': 'terima kasih',
        'mksh': 'terima kasih',
        'mantap': 'mantap',
        'mantul': 'mantap betul',
        'mantabs': 'mantap',
        'sip': 'sippp',
        'rekomen': 'rekomendasi',
        'recommended': 'rekomendasi',
        'recommend': 'rekomendasi',
        'fast': 'cepat',
        'respon': 'respons',
        'response': 'respons',
        'bagus': 'baik',
        'good': 'baik',
        'nice': 'baik',
        'jelek': 'buruk',
        'bad': 'buruk'
    }

def preprocess_text(text: str) -> str:
    """Preprocessing teks bahasa Indonesia"""
    try:
        # Ubah ke lowercase
        text = text.lower()
        
        # Hapus emoticon dan simbol
        text = re.sub(r'[^\w\s]', ' ', text)
        
        # Hapus angka
        text = re.sub(r'\d+', '', text)
        
        # Hapus whitespace berlebih
        text = ' '.join(text.split())
        
        # Load kamus slang
        slang_dict = load_slang_dict()
        
        # Ganti kata slang dengan kata baku
        words = text.split()
        corrected_words = [slang_dict.get(word, word) for word in words]
        
        # Gabung kembali menjadi kalimat
        corrected_text = ' '.join(corrected_words)
        
        return corrected_text
        
    except Exception as e:
        print(f"Error in preprocessing: {str(e)}")
        return text

def analyze_sentiment(text):
    try:
        # Preprocessing teks
        preprocessed_text = preprocess_text(text)
        
        # Inisialisasi translator
        translator = Translator()
        
        # Translate dari Indonesia ke Inggris
        translated = translator.translate(preprocessed_text, src='id', dest='en')
        english_text = translated.text
        
        # Analisis sentimen menggunakan TextBlob
        blob = TextBlob(english_text)
        
        # Identifikasi kata-kata positif kuat
        strong_positive_words = [
            'very good', 'excellent', 'professional', 'friendly',
            'perfect', 'amazing', 'great', 'awesome', 'outstanding',
            'helpful', 'fast', 'quick', 'responsive', 'reliable',
            'recommended', 'satisfied', 'satisfying', 'wonderful',
            'best', 'superb', 'exceptional', 'fantastic', 'brilliant',
            'impressive', 'remarkable', 'outstanding', 'excellent service',
            'good service', 'good communication', 'on time', 'punctual',
            'trustworthy', 'trusted', 'skilled', 'expert', 'experienced',
            'quality', 'smooth', 'efficient', 'effective'
        ]
        
        # Identifikasi kata-kata negatif atau limitasi
        negative_words = [
            'but', 'however', 'still', 'can be improved', 'not',
            'slow', 'late', 'delay', 'delayed', 'poor', 'bad',
            'terrible', 'worst', 'disappointing', 'disappointed',
            'unprofessional', 'unreliable', 'inconsistent',
            'expensive', 'overpriced', 'costly', 'waste',
            'problem', 'issue', 'error', 'mistake', 'wrong',
            'difficult', 'complicated', 'confusing', 'confused',
            'unfortunately', 'sadly', 'despite', 'although',
            'mediocre', 'average', 'ordinary', 'lacking',
            'unresponsive', 'ignored', 'rude', 'unfriendly',
            'dissatisfied', 'unsatisfied', 'unhappy'
        ]
        
        base_score = blob.sentiment.polarity
        
        # Tambahkan bobot untuk kata-kata positif kuat
        for word in strong_positive_words:
            if word.lower() in english_text.lower():
                base_score += 0.1  # Menambah skor untuk setiap kata positif kuat
        
        # Kurangi skor untuk kata-kata negatif
        for word in negative_words:
            if word.lower() in english_text.lower():
                base_score -= 0.1  # Mengurangi skor untuk setiap kata negatif
        
        # Normalisasi skor agar tetap dalam rentang -1 hingga 1
        final_score = max(min(base_score, 1.0), -1.0)
        
        # Ubah skala dari -1,1 menjadi 0-5
        score = ((final_score + 1) / 2) * 5
        
        return score
    except Exception as e:
        print(f"Error in sentiment analysis: {str(e)}")
        return 2.5  # Return nilai netral jika ada error

def calculate_agent_score(agent_id):
    conn = get_db_connection()
    if not conn:
        return 0
        
    cursor = conn.cursor(dictionary=True)
    
    try:
        # Ambil nama agent
        cursor.execute("SELECT nama FROM agent WHERE id_agent = %s", (agent_id,))
        agent_name = cursor.fetchone()['nama']
        print("\n" + "="*50)
        print(f"ANALISIS AGENT: {agent_name} (ID: {agent_id})")
        print("="*50)
        
        query = """
            SELECT u.rating, u.ulasan 
            FROM ulasan u
            JOIN pesanan p ON u.id_pesanan = p.id_pesanan
            WHERE p.id_agent = %s
        """
        
        cursor.execute(query, (agent_id,))
        reviews = cursor.fetchall()
        
        if not reviews:
            print("Tidak ada ulasan untuk agent ini")
            return 0
        
        print(f"\nJumlah ulasan: {len(reviews)}")
        print("-"*50)
        
        total_score = 0
        for idx, review in enumerate(reviews, 1):
            print(f"\nULASAN #{idx}")
            print(f"Rating asli: {review['rating']}/5")
            print(f"Teks original: {review['ulasan']}")
            
            # Preprocessing
            preprocessed_text = preprocess_text(review['ulasan'])
            print(f"Hasil preprocessing: {preprocessed_text}")
            
            # Translasi & Analisis Sentimen
            translator = Translator()
            translated = translator.translate(preprocessed_text, src='id', dest='en')
            english_text = translated.text
            print(f"Hasil translasi: {english_text}")
            
            sentiment_score = analyze_sentiment(review['ulasan'])
            print(f"Skor sentimen: {sentiment_score:.2f}/5")
            
            # Kombinasikan rating dan sentimen
            rating_score = float(review['rating'])
            review_score = (rating_score * 0.7) + (sentiment_score * 0.3)
            print(f"Skor akhir ulasan (70% rating + 30% sentimen): {review_score:.2f}/5")
            
            total_score += review_score
        
        average_score = total_score / len(reviews)
        print("\n" + "-"*50)
        print(f"SKOR AKHIR AGENT {agent_name}: {average_score:.2f}/5")
        print("-"*50 + "\n")
        
        return average_score
        
    except Exception as e:
        print(f"Error calculating score: {str(e)}")
        return 0
    finally:
        cursor.close()
        conn.close()

def format_game_name(game: str) -> str:
    game_formats = {
        'arknights': 'Arknights',
        'genshinimpact': 'Genshin Impact',
        'honkaiimpact': 'Honkai Impact',
        'honkaistarrail': 'Honkai Star Rail', 
        'mobilelegends': 'Mobile Legends',
        'valorant': 'Valorant',
        'zenlesszonezero': 'Zenless Zone Zero'
    }
    return game_formats.get(game.lower(), game)

@app.route('/get_agent_recommendations', methods=['GET', 'POST'])
def get_agent_recommendations():
    try:
        if request.method == 'POST':
            data = request.json
            game = data.get('game')
        else:
            game = request.args.get('game')
        
        if not game:
            return jsonify({"error": "Game parameter is required"}), 400
            
        # Format nama game
        formatted_game = format_game_name(game)
        print(f"Original game parameter: {game}")
        print(f"Formatted game name: {formatted_game}")
            
        conn = get_db_connection()
        if not conn:
            return jsonify({"error": "Database connection failed"}), 500
            
        cursor = conn.cursor(dictionary=True)
        
        query = """
            SELECT a.id_agent, a.nama, a.game,
                   COUNT(DISTINCT u.id_ulasan) as review_count,
                   AVG(u.rating) as avg_rating
            FROM agent a
            LEFT JOIN pesanan p ON a.id_agent = p.id_agent
            LEFT JOIN ulasan u ON p.id_pesanan = u.id_pesanan
            WHERE a.game LIKE %s
            GROUP BY a.id_agent
        """
        
        cursor.execute(query, (f"%{formatted_game}%",))
        agents = cursor.fetchall()
        
        for agent in agents:
            agent['score'] = calculate_agent_score(agent['id_agent'])
        
        sorted_agents = sorted(agents, key=lambda x: x['score'], reverse=True)
        
        cursor.close()
        conn.close()
        
        return jsonify(sorted_agents)
        
    except Exception as e:
        print(f"Error in get_agent_recommendations: {str(e)}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000, host='0.0.0.0', use_reloader=True) 