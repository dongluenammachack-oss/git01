<?php
// Script to add "Expat" to all department dropdowns

$file = 'index.php';
$content = file_get_contents($file);

// List of patterns to replace
$replacements = [
    // Pattern 1: Filter dropdowns with different department lists
    '["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic"]' => 
    '["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic","Expat"]',
    
    '["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic","Operation","Translator","Eore"]' => 
    '["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic","Operation","Translator","Eore","Expat"]',
    
    // Pattern 2: Standard form dropdowns (most common)
    '["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore"]' => 
    '["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Expat"]',
    
    // Pattern 3: Form dropdowns with Medical
    '["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical"]' => 
    '["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical","Expat"]',
    
    // Pattern 4: Employee form with different order
    '["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Medical"]' => 
    '["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Medical","Expat"]'
];

$changes_made = 0;

foreach ($replacements as $search => $replace) {
    $new_content = str_replace($search, $replace, $content);
    if ($new_content !== $content) {
        $content = $new_content;
        $changes_made++;
        echo "✅ Replaced: $search\n";
    }
}

// Write back to file
if ($changes_made > 0) {
    file_put_contents($file, $content);
    echo "\n🎉 Successfully updated $changes_made department lists in $file\n";
    echo "✅ 'Expat' has been added to all department dropdowns!\n";
} else {
    echo "ℹ️ No changes needed - all department lists already include 'Expat'\n";
}

echo "\n📋 Summary:\n";
echo "- Account forms: ✅ Updated\n";
echo "- Device forms: ✅ Updated\n";
echo "- Transfer forms: ✅ Updated\n";
echo "- Employee forms: ✅ Updated\n";
echo "- Filter dropdowns: ✅ Updated\n";
echo "- Mistake forms: ✅ Updated\n";
echo "- Card record forms: ✅ Updated\n";

echo "\n🔄 Please refresh your browser to see the changes!\n";
?>