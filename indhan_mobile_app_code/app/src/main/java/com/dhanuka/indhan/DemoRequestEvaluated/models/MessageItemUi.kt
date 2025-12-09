package com.dhanuka.indhan.DemoRequestEvaluated.models

class MessageItemUi(val content: String, val messageType: Int, val date: String, val name: String) {
    companion object {
        const val TYPE_MY_MESSAGE = 0
        const val TYPE_FRIEND_MESSAGE = 1
    }
}