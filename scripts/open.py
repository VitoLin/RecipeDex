# takes json dataset and transforms it to csv's that fit our project database
import json

# Opening JSON file
# f = open('./recipes_with_nutritional_info.json', 'r')
f = open('./recipes_with_nutritional_info.json', 'r')

f1 = open('./csv/recipes.csv', 'w')
f2 = open('./csv/directions.csv', 'w')
f3 = open('./csv/ingredients.csv', 'w')
f4 = open('./csv/nutrition.csv', 'w')

# returns JSON object as
# a dictionary
data = json.load(f)

recipe_list = list(data)

f1.write("id;name;url;weight\n")
f2.write("id;step_number;direction\n")
f3.write("id;ingredient_number;ingredient;amount;weight\n")
f4.write("id;calories;protein;carbohydrate;fat\n")

for recipe in recipe_list:
    f1.write(recipe["id"] + ";" + recipe["title"].replace(";", "") + ";" + recipe["url"] + ";" + str(round(sum(recipe["weight_per_ingr"]), 2)) + "\n")

    for i in range(len(recipe["instructions"])):
        f2.write(recipe["id"] + ";" + str(i+1) + ";" + recipe["instructions"][i]["text"].replace(";", "")+ "\n")

    for i in range(len(recipe["ingredients"])):
        f3.write(recipe["id"] + ";" + str(i+1) + ";" + recipe["ingredients"][i]["text"].replace(";", "") + ";" + recipe["quantity"][i]["text"].replace(";", "") + ";" + recipe["unit"][i]["text"] + ";" + str(round(recipe["weight_per_ingr"][i], 2)) + "\n")
    
    carbs = round((recipe["nutr_values_per100g"]["energy"] - recipe["nutr_values_per100g"]["fat"] * 9 - recipe["nutr_values_per100g"]["protein"] * 4)/4, 2)
    if carbs < 0:
        carbs = 0
    f4.write(recipe["id"] + ";" + str(round(recipe["nutr_values_per100g"]["energy"], 2)) + ";"  + str(round(recipe["nutr_values_per100g"]["protein"], 2)) + ";" +  str(carbs) + ";"  + str(round(recipe["nutr_values_per100g"]["fat"], 2)) + "\n")



# Closing file
f.close()
f1.close()
f2.close()
f3.close()
f4.close()

