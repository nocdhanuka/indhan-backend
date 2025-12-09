package com.dhanuka.indhan.support.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.QuestionModel

class QuestionAdapter(val questions: ArrayList<QuestionModel>) :
    RecyclerView.Adapter<QuestionAdapter.ViewHolder>() {

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val textViewName = itemView.findViewById(R.id.questionTextView) as TextView
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val v =
            LayoutInflater.from(parent?.context).inflate(R.layout.layout_question, parent, false)
        return ViewHolder(v)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val question: QuestionModel = questions[position]
        val params: LinearLayout.LayoutParams =
            holder.textViewName.layoutParams as LinearLayout.LayoutParams
        holder.textViewName.text = question.name
        holder.textViewName.background = question.bgLayout
        params.gravity = question.gravity
        holder.textViewName.gravity = 1

    }

    override fun getItemCount(): Int {
        return questions.size
    }


}