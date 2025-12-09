package com.dhanuka.indhan.support.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.SubmitResponseModel
import kotlinx.android.synthetic.main.option_button.view.*

class SubmissionAdapter(val submit: List<SubmitResponseModel>) :
    RecyclerView.Adapter<SubmissionAdapter.ViewHolder>() {
    private lateinit var clickListener: SubmitClickListener

    inner class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        init {
            itemView.optionButton
                .setOnClickListener { view ->
                    System.out.println("sub " + adapterPosition)
                    if ((submit != null) && (submit.size > adapterPosition) && (adapterPosition != -1)) {
                        clickListener.onClick(view, submit[adapterPosition])

                    }
                }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val v = LayoutInflater.from(parent?.context).inflate(R.layout.option_button, parent, false)
        return ViewHolder(v)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val submit: SubmitResponseModel = submit[position]
        holder.itemView.optionButton.text = submit.submissionName

    }

    override fun getItemCount(): Int {
        return submit.size
    }

    fun setClickListener(itemClickListener: SubmitClickListener) {
        this.clickListener = itemClickListener
    }

    interface SubmitClickListener {
        fun onClick(view: View, submit: SubmitResponseModel)
    }
}