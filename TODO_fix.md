# Fix AI Analysis Thought Signature Error

## Steps
1. Update `config/gemini.php`:
   - Change model to `gemini-2.0-flash`
   - Update API URL to v1beta
   - Add `response_mime_type: "application/json"`
   - Remove deprecated `curl_close`

2. Update `api/analyze-text.php`:
   - Update URL to use new model and version
   - Add `response_mime_type`
   - Improve `extractJsonFromText` to handle thought signatures
   - Remove deprecated `curl_close`

3. Update `api/analyze-document.php`:
   - Ensure it uses the updated `callGeminiAPI` (no changes needed)
   - Possibly improve JSON extraction.

4. Test the changes.

## Progress
- [x] Step 1
- [x] Step 2
- [x] Step 3
- [ ] Step 4
