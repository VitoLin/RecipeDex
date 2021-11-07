from recipe_scrapers import scrape_me
import mysql.connector
import hashlib
import csv
import re
import sys

# remove annoying characters
chars = {
    '\xc2\x82'.encode('latin-1').decode('utf-8') : ',',        # High code comma
    '\xc2\x84'.encode('latin-1').decode('utf-8') : ',,',       # High code double comma
    '\xc2\x85'.encode('latin-1').decode('utf-8') : '...',      # Tripple dot
    '\xc2\x88'.encode('latin-1').decode('utf-8') : '^',        # High carat
    '\xc2\x91'.encode('latin-1').decode('utf-8') : '\x27',     # Forward single quote
    '\xc2\x92'.encode('latin-1').decode('utf-8') : '\x27',     # Reverse single quote
    '\xc2\x93'.encode('latin-1').decode('utf-8') : '\x22',     # Forward double quote
    '\xc2\x94'.encode('latin-1').decode('utf-8') : '\x22',     # Reverse double quote
    '\xc2\x95'.encode('latin-1').decode('utf-8') : ' ',
    '\xc2\x96'.encode('latin-1').decode('utf-8') : '-',        # High hyphen
    '\xc2\x97'.encode('latin-1').decode('utf-8') : '--',       # Double hyphen
    '\xc2\x99'.encode('latin-1').decode('utf-8') : ' ',
    '\xc2\xa0'.encode('latin-1').decode('utf-8') : ' ',
    '\xc2\xa6'.encode('latin-1').decode('utf-8') : '|',        # Split vertical bar
    '\xc2\xab'.encode('latin-1').decode('utf-8') : '<<',       # Double less than
    '\xc2\xbb'.encode('latin-1').decode('utf-8') : '>>',       # Double greater than
    # vulgar fractions
    '\xc2\xbc'.encode('latin-1').decode('utf-8') : '1/4',      # one quarter
    '\xc2\xbd'.encode('latin-1').decode('utf-8') : '1/2',      # one half
    '\xc2\xbe'.encode('latin-1').decode('utf-8') : '3/4',      # three quarters
    '\xe2\x85\x90'.encode('latin-1').decode('utf-8') : '1/7',
    '\xe2\x85\x91'.encode('latin-1').decode('utf-8') : '1/9',
    '\xe2\x85\x92'.encode('latin-1').decode('utf-8') : '1/10',
    '\xe2\x85\x93'.encode('latin-1').decode('utf-8') : '1/3',
    '\xe2\x85\x94'.encode('latin-1').decode('utf-8') : '2/3',
    '\xe2\x85\x95'.encode('latin-1').decode('utf-8') : '1/5',
    '\xe2\x85\x96'.encode('latin-1').decode('utf-8') : '2/5',
    '\xe2\x85\x97'.encode('latin-1').decode('utf-8') : '3/5',
    '\xe2\x85\x98'.encode('latin-1').decode('utf-8') : '4/5',
    '\xe2\x85\x99'.encode('latin-1').decode('utf-8') : '1/6',
    '\xe2\x85\x9A'.encode('latin-1').decode('utf-8') : '5/6',
    '\xe2\x85\x9b'.encode('latin-1').decode('utf-8') : '1/8',
    '\xe2\x85\x9c'.encode('latin-1').decode('utf-8') : '3/8',
    '\xe2\x85\x9d'.encode('latin-1').decode('utf-8') : '5/8',
    '\xe2\x85\x9e'.encode('latin-1').decode('utf-8') : '7/8',

    '\xe2\x80\x99'.encode('latin-1').decode('utf-8') : '',
    '\xe2\x80\x9c'.encode('latin-1').decode('utf-8') : '',

    '\xca\xbf'.encode('latin-1').decode('utf-8') : '\x27',     # c-single quote
    '\xcc\xa8'.encode('latin-1').decode('utf-8') : '',         # modifier - under curve
    '\xcc\xb1'.encode('latin-1').decode('utf-8') : ''          # modifier - under line
}
def replace_chars(match):
    char = match.group(0)
    return chars[char]

def format(instr):
    out = re.sub('(' + '|'.join(chars.keys()) + ')', replace_chars, instr.replace("\'", "")).encode("ascii", "ignore").decode()
    return out

def toFloat(instr):
    float_string = ''.join(re.findall("\d*\.?\d+", instr))
    out = 0
    if float_string:
        out = float(float_string)

    return out

# insert into mysql
mydb = mysql.connector.connect(
    host="localhost",
    user="vlin2",
    password="123",
    database="vlin2"
)

mycursor = mydb.cursor()

def executeSQL(sql):
    try:
        mycursor.execute(sql)
        mydb.commit()
    except:
        # print(sql)
        return

def insertURL(url, expressive=False):
    try:
        scraper = scrape_me(url, wild_mode=True)
        title = scraper.title()
        if title == 'Access Denied':
            print("skipped")
            return
    except:
        print("skipped")
        return

    try:
        image = scraper.image()
    except:
        print("no image")
        image = ""

    if expressive:
        print(title)
        print(url)

    hash_object = hashlib.sha256(url.encode('utf-8'))
    id = hash_object.hexdigest()

    num_ingredients = 1
    for ingredient in scraper.ingredients():
        sql = f"INSERT IGNORE INTO ingredients VALUES ('{id}',{num_ingredients}, '{format(ingredient)}');"
        executeSQL(sql)
        num_ingredients+=1
        if expressive:
            print(ingredient)


    num_instructions = 1
    for direction in scraper.instructions().split("\n"):
        if not direction.isspace() :
            sql = f"INSERT IGNORE INTO directions VALUES ('{id}',{num_instructions}, '{format(direction)}');"
            executeSQL(sql)
            num_instructions+=1
        if expressive:
            print(direction)

    sql = f"INSERT IGNORE INTO recipes VALUES ('{id}', '{format(title)}', '{format(url)}', '{format(image)}',{num_ingredients},{num_instructions});"
    executeSQL(sql)

    try:
        sql = f"INSERT IGNORE INTO nutrients VALUES ('{id}', {0}, {0}, {0}, {0}, {0}, {0}, {0}, {0}, {0});"
        executeSQL(sql)

        nutrients = scraper.nutrients()
        for key in nutrients.keys():
            sql = f"UPDATE nutrients SET {key} = {toFloat(nutrients[key])} WHERE id = '{id}';"
            executeSQL(sql)
    except:
        print("no nutrients")

    if expressive and nutrients:
            print(nutrients)

    return id

def deleteID(id): 
    sql = f"delete from recipes where id = '{id}';"
    executeSQL(sql)
    sql = f"delete from ingredients where id = '{id}';"
    executeSQL(sql)
    sql = f"delete from directions where id = '{id}';"
    executeSQL(sql)
    sql = f"delete from nutrients where id = '{id}';"
    executeSQL(sql)
    return id


if sys.argv[1] == "i":
    url = sys.argv[2]
    print(insertURL(url, True))

if sys.argv[1] == "d":
    print(deleteID(sys.argv[2]))


# get all urls from recipes csv
# with open('./csv/recipes.csv') as csv_file:
#     csv_reader = csv.reader(csv_file, delimiter=';')
#     line_count = 0
#     for row in csv_reader:
#         if line_count == 0:
#             line_count += 1
#         else:
#             url = row[2]
#             insertURL(url)
#             line_count += 1
#             print(f'Processed {line_count} lines.')
