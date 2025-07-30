<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use App\Models\Question;
use Illuminate\Database\Seeder;

class LanguageSkillQuestionsSeeder extends Seeder
{
    public function run()
    {
        $languageCategory = SkillCategory::where('slug', 'language')->first();

        if (!$languageCategory) {
            $this->command->error("Language skill category not found. Make sure it's seeded.");
            return;
        }

        $questions = [
            // Age 2
            ['age' => 2, 'text' => 'Follow two-step commands'],
            ['age' => 2, 'text' => "Understand 'me' and 'you'"],
            ['age' => 2, 'text' => 'Point to 5 to 10 pictures'],
            ['age' => 2, 'text' => 'Use two-word sentences (noun + verb)'],
            ['age' => 2, 'text' => 'Speak using telegraphic speech'],
            ['age' => 2, 'text' => 'Use 50+ words'],
            ['age' => 2, 'text' => 'Be understood 50% of the time'],
            ['age' => 2, 'text' => 'Refer to self by name'],
            ['age' => 2, 'text' => 'Name at least three pictures'],

            // Age 3
            ['age' => 3, 'text' => 'Point to parts of pictures (e.g., nose of cow, door of car)'],
            ['age' => 3, 'text' => 'Name body parts by function'],
            ['age' => 3, 'text' => 'Understand negatives'],
            ['age' => 3, 'text' => 'Group objects like foods and toys'],
            ['age' => 3, 'text' => 'Use 200+ words'],
            ['age' => 3, 'text' => 'Use three-word sentences'],
            ['age' => 3, 'text' => 'Use pronouns correctly'],
            ['age' => 3, 'text' => 'Be understood 75% of the time'],
            ['age' => 3, 'text' => 'Use plural forms of words'],
            ['age' => 3, 'text' => 'Ask to be read to'],

            // Age 4
            ['age' => 4, 'text' => 'Follow three-step commands'],
            ['age' => 4, 'text' => 'Point to things that are the same or different'],
            ['age' => 4, 'text' => 'Name things when actions are described (e.g., swims in water, cut with it)'],
            ['age' => 4, 'text' => 'Understand adjectives like bushy, long, thin, pointed'],
            ['age' => 4, 'text' => 'Use 300–1000 words'],
            ['age' => 4, 'text' => 'Tell simple stories'],
            ['age' => 4, 'text' => 'Be understood 100% of the time'],
            ['age' => 4, 'text' => 'Use feeling-related words'],
            ['age' => 4, 'text' => "Use time-related words like 'yesterday' or 'later'"],

            // Age 5
            ['age' => 5, 'text' => 'Know right and left on self'],
            ['age' => 5, 'text' => 'Point to the different one in a series'],
            ['age' => 5, 'text' => "Understand 'er' endings like batter or skater"],
            ['age' => 5, 'text' => 'Understand adjectives like busy, long, thin, pointed'],
            ['age' => 5, 'text' => 'Enjoy rhyming words and alliterations'],
            ['age' => 5, 'text' => 'Produce words that rhyme'],
            ['age' => 5, 'text' => "Point correctly to 'side', 'middle', and 'corner'"],
            ['age' => 5, 'text' => 'Repeat six-to-eight-word sentences'],
            ['age' => 5, 'text' => 'Define simple words'],
            ['age' => 5, 'text' => 'Use 2,000 words'],
            ['age' => 5, 'text' => 'Know their telephone number'],
            ['age' => 5, 'text' => "Respond to 'why' questions"],
            ['age' => 5, 'text' => 'Retell stories with clear beginning, middle, and end'],

            // Age 6
            ['age' => 6, 'text' => 'Ask and answer questions to gather information or clarify something'],
            ['age' => 6, 'text' => 'Identify who is telling a story'],
            ['age' => 6, 'text' => 'Compare and contrast the adventures and experiences of story characters'],
            ['age' => 6, 'text' => 'Explain major differences between a story and information'],
            ['age' => 6, 'text' => 'Identify the main topic and key details of a text'],
            ['age' => 6, 'text' => 'Describe the connection between two individuals, events, ideas, or pieces of information'],
            ['age' => 6, 'text' => 'Identify similarities and differences between two texts on the same topic'],
            ['age' => 6, 'text' => 'Determine the meaning of unknown or multiple-meaning words using sentence-level context'],
            ['age' => 6, 'text' => 'Use frequently occurring affixes to understand word meanings'],
            ['age' => 6, 'text' => 'Use common root words and their inflectional forms correctly'],
            ['age' => 6, 'text' => 'Sort words into categories and define them by key attributes'],
            ['age' => 6, 'text' => 'Distinguish shades of meaning among similar verbs and adjectives'],
            ['age' => 6, 'text' => 'Identify real-life connections between words and their usage'],
            ['age' => 6, 'text' => 'Participate in classroom activities by asking and answering questions'],
            ['age' => 6, 'text' => 'Retell stories with key details and a central message'],
            ['age' => 6, 'text' => 'Describe characters, settings, and major story events'],
            ['age' => 6, 'text' => 'Orally tell stories or past events with proper sequence'],
            ['age' => 6, 'text' => 'Orally provide information with a topic, facts, and closure'],
            ['age' => 6, 'text' => 'Orally state opinions with a topic, reason, and closure'],
            ['age' => 6, 'text' => 'Use common, proper, and possessive nouns when speaking'],
            ['age' => 6, 'text' => 'Use determiners correctly'],
            ['age' => 6, 'text' => 'Produce and expand simple and compound sentences'],
            ['age' => 6, 'text' => 'Match singular/plural nouns with correct verbs'],
            ['age' => 6, 'text' => 'Use personal, possessive, and indefinite pronouns correctly'],
            ['age' => 6, 'text' => 'Use correct verb tenses (past, present, future)'],
            ['age' => 6, 'text' => 'Use adjectives, conjunctions, and prepositions appropriately'],

            // Age 7
            ['age' => 7, 'text' => 'Ask and answer questions to show understanding or gather more information'],
            ['age' => 7, 'text' => 'Compare formal and informal uses of English'],
            ['age' => 7, 'text' => 'Use sentence context to figure out word meanings'],
            ['age' => 7, 'text' => 'Use root words to understand unfamiliar related words (e.g., addition, additional)'],
            ['age' => 7, 'text' => "Understand new words with familiar prefixes (e.g., 'unhappy')"],
            ['age' => 7, 'text' => 'Predict meanings of compound words (e.g., birdhouse)'],
            ['age' => 7, 'text' => 'Distinguish similar verbs and adjectives (e.g., toss, throw, hurl)'],
            ['age' => 7, 'text' => 'Identify real-life connections between words and their use'],
            ['age' => 7, 'text' => 'Tell stories or experiences with facts and descriptive details'],
            ['age' => 7, 'text' => 'Provide information with a topic, facts, and concluding statement'],
            ['age' => 7, 'text' => "State opinions using linking words like 'because'"],
            ['age' => 7, 'text' => "Use collective nouns like 'group' or 'everyone'"],
            ['age' => 7, 'text' => "Use irregular plural nouns like 'feet', 'children', and 'teeth'"],
            ['age' => 7, 'text' => "Use reflexive pronouns like 'myself', 'yourself', 'themselves'"],
            ['age' => 7, 'text' => "Use irregular past tense verbs like 'sat', 'hid', and 'told'"],
            ['age' => 7, 'text' => 'Use adjectives and adverbs appropriately based on what they modify'],
            ['age' => 7, 'text' => 'Produce, expand, and rearrange complete simple and compound sentences'],

            // Age 8
            ['age' => 8, 'text' => 'Ask and answer questions to clarify unclear topics'],
            ['age' => 8, 'text' => 'Use sentence context to understand unfamiliar or multiple-meaning words'],
            ['age' => 8, 'text' => 'Understand root words and affixes to guess meanings'],
            ['age' => 8, 'text' => 'Use a dictionary to clarify word meanings'],
            ['age' => 8, 'text' => "Distinguish literal and nonliteral meanings (e.g., 'take their seats')"],
            ['age' => 8, 'text' => 'Distinguish shades of meaning among mental state or certainty words (e.g., suspected vs. knew)'],
            ['age' => 8, 'text' => 'Identify real-life examples for descriptive words (e.g., friendly, helpful)'],
            ['age' => 8, 'text' => 'Explain the function of nouns, pronouns, verbs, adjectives, and adverbs'],
            ['age' => 8, 'text' => 'Tell or retell real or imagined stories with clear sequence and details'],
            ['age' => 8, 'text' => 'Clearly convey ideas and information with linking words and closure'],
            ['age' => 8, 'text' => 'State opinions with supporting reasons'],
            ['age' => 8, 'text' => 'Form and use regular/irregular and abstract nouns'],
            ['age' => 8, 'text' => 'Use regular and irregular verb tenses'],
            ['age' => 8, 'text' => 'Use correct subject-verb and pronoun-antecedent agreement'],
            ['age' => 8, 'text' => "Use possessives like 'his', 'her', and possessive 's"],
            ['age' => 8, 'text' => 'Use comparatives and superlatives (e.g., bigger, fastest)'],
            ['age' => 8, 'text' => 'Speak using simple, compound, and complex sentences'],

            // Age 9
            ['age' => 9, 'text' => 'Ask and answer questions to demonstrate understanding or clarify'],
            ['age' => 9, 'text' => 'Use sentence-level clues to determine meanings of words or phrases'],
            ['age' => 9, 'text' => 'Break down words into root and affixes to infer meanings'],
            ['age' => 9, 'text' => 'Use a dictionary to check word meanings'],
            ['age' => 9, 'text' => 'Tell the difference between literal and figurative meanings'],
            ['age' => 9, 'text' => 'Distinguish shades of meaning among similar descriptive words'],
            ['age' => 9, 'text' => 'Connect words to real-life examples (e.g., spicy, juicy)'],
            ['age' => 9, 'text' => 'Explain the function of basic grammar components'],
            ['age' => 9, 'text' => 'Tell or retell real or imaginary experiences with proper structure'],
            ['age' => 9, 'text' => 'Give information clearly using facts and linking words'],
            ['age' => 9, 'text' => 'State and support opinions in speech'],
            ['age' => 9, 'text' => 'Use and understand various types of nouns and verbs'],
            ['age' => 9, 'text' => 'Maintain correct agreement between subjects and verbs'],
            ['age' => 9, 'text' => 'Use possessive forms correctly'],
            ['age' => 9, 'text' => 'Use correct forms of comparatives and superlatives'],
            ['age' => 9, 'text' => 'Speak in a variety of sentence structures'],

            // Age 12
            ['age' => 12, 'text' => 'Attempt to use complex or sophisticated vocabulary'],
            ['age' => 12, 'text' => "Use complex conjunctions like 'meanwhile'"],
            ['age' => 12, 'text' => 'Tell detailed and entertaining stories with subplots'],
            ['age' => 12, 'text' => 'Negotiate by suggesting alternatives or outcomes'],
            ['age' => 12, 'text' => "Recognize when someone doesn’t understand and explain again"],
            ['age' => 12, 'text' => 'Understand sarcasm when it is obvious'],
            ['age' => 12, 'text' => 'Understand and respond to different question types (e.g., open, rhetorical)'],
            ['age' => 12, 'text' => 'Recognize idioms, even if not fully grasping their meaning'],
            ['age' => 12, 'text' => 'Recognize when a sentence is grammatically incorrect'],
        ];

        foreach ($questions as $question) {
            Question::create([
                'skill_category_id' => $languageCategory->id,
                'text' => $question['text'],
                'age' => $question['age'],
            ]);
        }

        $this->command->info('Language Skill questions seeded successfully.');
    }
}
