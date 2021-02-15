
# running sample with various images and configs
#for image in ../../testdata/passport_rf_2.jpg; do 
#  config=$(ls ../../data-zip/*.zip) # assuming one file
  #for document_types in "rus.passport.*" "mrz.*" "rus.drvlic.*" "*"; do
#  for document_types in "mrz.*"; do
    #php -c php.ini smartid_sample.php "$image" "$config" "$document_types"
#  done
#done

#php -c php.ini smartid_sample.php testdata/katya_back.jpg bundle_kaz_mrz_server.zip mrz.*

php SmartID.php testdata/katya_back.jpg bundle_kaz_mrz_server.zip mrz.*