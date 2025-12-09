package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.DemoRequestEvaluated.models.MessageItemUi
import com.dhanuka.indhan.DemoRequestEvaluated.models.MessageItemUi.Companion.TYPE_MY_MESSAGE
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility

class ChatAdapter(var data: MutableList<MessageItemUi>) :
    RecyclerView.Adapter<MessageViewHolder<*>>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MessageViewHolder<*> {
        val context = parent.context
        return when (viewType) {
            TYPE_MY_MESSAGE -> {
                val view =
                    LayoutInflater.from(context).inflate(R.layout.my_message_item, parent, false)
                MyMessageViewHolder(view)
            }
            else -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.friend_message_item, parent, false)
                FriendMessageViewHolder(view)
            }
        }
    }

    override fun onBindViewHolder(holder: MessageViewHolder<*>, position: Int) {
        val item = data[position]
        when (holder) {
            is MyMessageViewHolder -> holder.bind(item)
            is FriendMessageViewHolder -> holder.bind(item)
            else -> throw IllegalArgumentException()
        }
    }

    override fun getItemCount(): Int = data.size

    override fun getItemViewType(position: Int): Int = data[position].messageType

    inner class MyMessageViewHolder(val view: View) : MessageViewHolder<MessageItemUi>(view) {
        private val messageContent = view.findViewById<TextView>(R.id.message)
        private val date = view.findViewById<TextView>(R.id.date)
        override fun bind(item: MessageItemUi) {
            val dates = Utility.formattedDate(item.date)
            messageContent.text = item.content
            date.text = dates
        }
    }

    inner class FriendMessageViewHolder(val view: View) : MessageViewHolder<MessageItemUi>(view) {
        private val messageContent = view.findViewById<TextView>(R.id.message)
        private val person = view.findViewById<TextView>(R.id.name_person)
        private val date = view.findViewById<TextView>(R.id.date)

        override fun bind(item: MessageItemUi) {
            messageContent.text = item.content
            val dates = Utility.formattedDate(item.date)
            person.text = item.name
            date.text = dates
        }
    }
}

abstract class MessageViewHolder<in T>(itemView: View) : RecyclerView.ViewHolder(itemView) {
    abstract fun bind(item: T)
}