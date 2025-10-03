# Quick Start Testing Guide

## Prerequisites
- Laravel application running
- Database migrated
- User account created

## Step-by-Step Testing

### 1. Start Required Services

Open 3 terminal windows:

**Terminal 1 - Reverb Server:**
```bash
cd jeplu
php artisan reverb:start --debug
```

**Terminal 2 - Laravel Server:**
```bash
cd jeplu
php artisan serve
```

**Terminal 3 - Vite Dev Server:**
```bash
cd jeplu
npm run dev
```

### 2. Create a Test Game

1. Navigate to `http://localhost:8000`
2. Login or register
3. Click "Create New Game"
4. Fill in game details:
   - Name: "Test Game"
   - Description: "Testing real-time gameplay"
5. Click "Create Game"

### 3. Add Categories and Questions

1. Click "Edit" on your test game
2. Add categories (e.g., "Science", "History", "Sports")
3. For each category, add questions with:
   - Question text
   - Answer
   - Points (100, 200, 300, etc.)
   - Time limit (30 seconds)
4. Save all questions

### 4. Start Game Session

1. Go back to Games dashboard
2. Click "Play" on your test game
3. You'll see the Teacher Control Interface

### 5. Add Teams

1. Click "Add Team" button
2. Create Team 1:
   - Name: "Blue Team"
   - Color: Blue
3. Create Team 2:
   - Name: "Red Team"
   - Color: Red
4. Note the buzzer codes displayed for each team

### 6. Open Presentation View

1. Click "Open Presentation View" button
2. A new tab opens with the game board
3. Position this on a second monitor or projector (or just another browser window)

### 7. Join as Teams

Open 2 more browser windows (or use mobile devices):

**Browser Window 1 (Blue Team):**
1. Navigate to `http://localhost:8000/play/team`
2. Enter Session Code (shown in teacher interface)
3. Enter Blue Team's buzzer code
4. Click "Join Game"

**Browser Window 2 (Red Team):**
1. Navigate to `http://localhost:8000/play/team`
2. Enter Session Code
3. Enter Red Team's buzzer code
4. Click "Join Game"

### 8. Start Playing

**In Teacher Interface:**
1. Click "Start Game"
2. Click on a question (e.g., $100 in Science)
3. Question appears on all screens
4. Click "Enable Buzzers"

**In Team Interfaces:**
1. Click "BUZZ IN!" button (or press spacebar)
2. First team to buzz shows in teacher interface

**In Teacher Interface:**
1. See which team buzzed first
2. Click "Select" on that team
3. Team's interface changes to their color
4. Click "Correct" or "Wrong" to award/deduct points
5. Click "Reveal Answer" to show the answer
6. Click "Close Question" to return to board

### 9. Continue Playing

Repeat step 8 for more questions:
- Different teams buzzing
- Testing correct and wrong answers
- Watching scores update in real-time

### 10. End Game

1. In Teacher Interface, click "End Game"
2. All screens show final scores
3. Winner is displayed

## What to Verify

### ✅ Real-time Updates
- [ ] Question appears on all screens simultaneously
- [ ] Buzzer button activates when enabled
- [ ] Buzz appears in teacher interface immediately
- [ ] Selected team's interface changes color
- [ ] Scores update on all screens
- [ ] Answer reveals on all screens

### ✅ Visual Feedback
- [ ] Green flash on correct answer
- [ ] Red flash on wrong answer
- [ ] Team color shows when selected
- [ ] Buzzers pulse when active
- [ ] Animations are smooth

### ✅ Functionality
- [ ] Multiple teams can buzz in order
- [ ] Response times are tracked
- [ ] Points are calculated correctly
- [ ] Questions can be marked as answered
- [ ] Session can be paused/resumed
- [ ] Game can be ended properly

### ✅ Mobile Experience
- [ ] Buzzer button is large and easy to tap
- [ ] No accidental scrolling or zooming
- [ ] Text is readable
- [ ] Colors are visible
- [ ] Interface is responsive

## Common Issues

### WebSocket Not Connecting
**Symptom**: Events not broadcasting, no real-time updates
**Solution**: 
1. Check Reverb server is running
2. Verify `.env` has correct Reverb settings
3. Check browser console for errors
4. Try refreshing the page

### Buzzer Not Working
**Symptom**: Clicking buzz button does nothing
**Solution**:
1. Ensure buzzers are enabled in teacher interface
2. Check that question is revealed
3. Verify team hasn't already buzzed
4. Check browser console for errors

### Scores Not Updating
**Symptom**: Points awarded but scores don't change
**Solution**:
1. Check WebSocket connection
2. Verify event is being broadcast (check Reverb debug output)
3. Refresh the page
4. Check database to see if score was saved

## Advanced Testing

### Test with Multiple Devices
1. Use actual mobile phones/tablets
2. Connect to same network
3. Use computer's IP address instead of localhost
4. Example: `http://192.168.1.100:8000/play/team`

### Test Network Issues
1. Throttle network in browser DevTools
2. Disconnect and reconnect WiFi
3. Verify auto-refresh works as fallback

### Test Edge Cases
1. Buzz in at exact same time (multiple devices)
2. Enable/disable buzzers rapidly
3. Award points to wrong team (then correct it)
4. Close question without revealing answer
5. End game with questions remaining

## Performance Testing

### Monitor Reverb Server
Watch the debug output for:
- Connection events
- Message broadcasts
- Any errors or warnings

### Check Browser Performance
Open DevTools and monitor:
- Network tab for WebSocket connection
- Console for any errors
- Performance tab for lag

### Database Queries
Check Laravel logs for:
- Number of queries per action
- Slow queries
- N+1 query issues

## Success Criteria

The feature is working correctly if:
1. ✅ All three interfaces (teacher, presentation, team) update in real-time
2. ✅ Buzzer functionality works smoothly
3. ✅ Scores calculate and display correctly
4. ✅ Visual feedback is clear and responsive
5. ✅ No errors in browser console or Reverb output
6. ✅ Mobile experience is smooth and intuitive
7. ✅ Multiple teams can play simultaneously
8. ✅ Game flow is logical and easy to follow

## Next Steps After Testing

1. Document any bugs found
2. Test fixes
3. Get user feedback
4. Optimize performance if needed
5. Deploy to staging environment
6. Final testing before production

## Support

If you encounter issues:
1. Check GAMEPLAY_IMPLEMENTATION.md for detailed documentation
2. Review Laravel Reverb documentation
3. Check Laravel Echo documentation
4. Review browser console for errors
5. Check Reverb server debug output

---

**Happy Testing! 🎮**