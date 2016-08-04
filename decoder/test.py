from decoder import decode_morse

txt_file = open('tests.txt', 'r')
tests_list = txt_file.readlines()
errors = 0
ok = 0
for test in tests_list:
    test = test.split("\t")
    fileName = "AudioFiles/" + test[0]
    result = test[1].strip()
    decoder_output = decode_morse(fileName)
    test_result = "Fail"
    if result == decoder_output:
        test_result = "Ok"
        ok += 1
    else:
        errors += 1
    print(test_result, fileName, result, decoder_output)
print("Tests:", len(tests_list))
print("Ok:", ok)
print("Fail", errors)
print("Accuracy:", (ok/len(tests_list))*100.)
