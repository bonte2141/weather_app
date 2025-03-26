import nltk
from nltk.sentiment.vader import SentimentIntensityAnalyzer
import mysql.connector
import json

nltk.download('vader_lexicon')


db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'weather_db'
}


CATEGORIES = {
    'bug': [
        'error', 'bug', 'not working', 'issue', 'problem', 'crash', 'fail', 'slow', 
        'laggy', 'unresponsive', 'freeze', 'glitch', 'stuck', 'unusable', 'flawed',
        'doesn\'t work', 'not responding', 'keeps crashing', 'unreliable', 
        'stopped working', 'freezes', 'locks up', 'system error', 'unexpected behavior'
    ],
    'request': [
        'add', 'want', 'suggest', 'missing', 'feature', 'please add', 'would like', 
        'could use', 'implement', 'feature request', 'would be great', 'enhancement', 
        'needs improvement', 'new function', 'should have', 'upgrade', 'more options'
    ],
    'admin': [
        'wrong', 'incorrect', 'position', 'inaccuracies', 
        'misplaced', 'accidentally', 'inappropriate', 'wrongly placed', 'incorrect info', 
        'outdated', 'put', 'wrong region', 'incorrect details', 'incorrectly categorized', 
        'should be removed', 'incorrect location', 'misclassified','wrong data'
    ]
}

def analyze_sentiment(text):
    sia = SentimentIntensityAnalyzer()
    score = sia.polarity_scores(text)

    if score['compound'] >= 0.05:
        return 'positive', score
    elif score['compound'] <= -0.05:
        return 'negative', score
    else:
        return 'neutral', score


def determine_category(text):
    text = text.lower()
    for category, keywords in CATEGORIES.items():
        if any(keyword in text for keyword in keywords):
            return category
    return 'general'


def process_feedback():
    try:
        db = mysql.connector.connect(**db_config)
        cursor = db.cursor(dictionary=True)

        
        cursor.execute("SELECT * FROM feedback WHERE sentiment IS NULL")
        feedbacks = cursor.fetchall()

        for feedback in feedbacks:
            text_to_analyze = feedback['content']

            
            sentiment, score = analyze_sentiment(text_to_analyze)

            
            category = determine_category(text_to_analyze)

            
            if category == 'bug':
                sentiment = 'negative'

            
            if category == 'admin':
                sentiment = 'negative'


            
            update_cursor = db.cursor()
            update_query = """
                UPDATE feedback 
                SET sentiment = %s, category = %s 
                WHERE id = %s
            """
            update_cursor.execute(update_query, (sentiment, category, feedback['id']))
            db.commit()
            update_cursor.close()

        
        cursor.execute("SELECT * FROM survey_feedback WHERE sentiment IS NULL")
        survey_feedbacks = cursor.fetchall()

        for survey in survey_feedbacks:
            content_json = json.loads(survey['content'])
            text_to_analyze = ' '.join(content_json.values())  

            
            scores = [analyze_sentiment(ans)[1]['compound'] for ans in content_json.values()]
            avg_score = sum(scores) / len(scores)

            
            if avg_score >= 0.05:
                sentiment = 'positive'
            elif avg_score <= -0.05:
                sentiment = 'negative'
            else:
                sentiment = 'neutral'

            
            rating_to_text = {
                5: "very good",
                4: "good",
                3: "average",
                2: "bad",
                1: "very bad"
            }

            rating_text = rating_to_text.get(survey['rating'], "neutral")
            rating_sentiment, _ = analyze_sentiment(rating_text)

            
            if rating_sentiment == 'positive' and sentiment == 'neutral':
                sentiment = 'positive'
            elif rating_sentiment == 'negative' and sentiment == 'neutral':
                sentiment = 'negative'
            elif rating_sentiment == 'neutral' and sentiment == 'neutral':
                sentiment = 'neutral'

            
            category = determine_category(text_to_analyze)

            
            update_cursor = db.cursor()
            update_query = """
                UPDATE survey_feedback 
                SET sentiment = %s 
                WHERE id = %s
            """
            update_cursor.execute(update_query, (sentiment, survey['id']))
            db.commit()
            update_cursor.close()

        cursor.close()
        db.close()

    except Exception as e:
        print(f"Error processing feedback: {str(e)}")


if __name__ == "__main__":
    process_feedback()
