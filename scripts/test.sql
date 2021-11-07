SET @id := '9d6fd9bab52e4d2f9904585bff7a432ee6fd08343871d7744c4bc36837b80fd1';
delete from recipes where id = @id;
delete from ingredients where id = @id;
delete from directions where id = @id;
delete from nutrients where id = @id;

delete from recipes;
delete from ingredients;
delete from directions;
delete from nutrients;

ALTER TABLE nutrients ALTER calories SET DEFAULT 0;
ALTER TABLE nutrients ALTER carbohydrateContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER cholesterolContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER fatContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER fiberContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER proteinContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER saturatedFatContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER sodiumContent SET DEFAULT 0;
ALTER TABLE nutrients ALTER sugarContent SET DEFAULT 0;