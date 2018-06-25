const { RTMClient } = require('@slack/client');

const Token = require('./config/Token.js');

const RTM = new RTMClient(Token.slack);
RTM.start();

RTM.on('message', (event) => {
    /* Botによる投稿 または 自分の投稿だった場合 は処理を行わない */
    if ((event.subtype && event.subtype === 'bot_message') ||
        (!event.subtype && event.user === RTM.activeUserId)) {
      return;
    }
    
    const message = event.text;
    if(message === '進捗どうですか') {
        const imageNumber = Math.floor( Math.random() * 22 + 1) ;
        RTM.sendMessage("https://raw.githubusercontent.com/nirot1r/NEW-GAME-Bot/master/images/" + imageNumber + ".jpg", event.channel)
            .catch(sendError => console.error(sendError));
    }
})