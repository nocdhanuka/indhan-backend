package com.dhanuka.indhan.support.adapter

import androidx.recyclerview.widget.RecyclerView
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.EditText
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.TextAnswer
import kotlinx.android.synthetic.main.layout_text_answer.view.*

class TextAnswerAdapter(val text: List<TextAnswer>) :
    RecyclerView.Adapter<TextAnswerAdapter.ViewHolder>() {

    private lateinit var clickListener: TextClickListener

    inner class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        init {
            var answerText = itemView.findViewById<EditText>(R.id.answerText)
            itemView.sendButton.setOnClickListener { view ->
                if (answerText.text != null || !TextUtils.isEmpty(answerText.text)) {
                    clickListener.onClick(
                        view,
                        TextAnswer(answerText.text.toString(), text[adapterPosition].proceedTo)
                    )
                }
            }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val v =
            LayoutInflater.from(parent?.context).inflate(R.layout.layout_text_answer, parent, false)
        return ViewHolder(v)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val answerText: TextAnswer = text[position]
        holder.itemView.findViewById<EditText>(R.id.answerText).hint = answerText.answerText
    }

    override fun getItemCount(): Int {
        return text.size
    }

    fun setClickListener(itemClickListener: TextClickListener) {
        this.clickListener = itemClickListener
    }

    interface TextClickListener {
        fun onClick(view: View, text: TextAnswer)
    }

}