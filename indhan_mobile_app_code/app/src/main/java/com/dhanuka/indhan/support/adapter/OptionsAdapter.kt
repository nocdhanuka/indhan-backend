package com.dhanuka.indhan.support.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.Option
import kotlinx.android.synthetic.main.option_button.view.*

class OptionsAdapter(val options: List<Option>) :
    RecyclerView.Adapter<OptionsAdapter.ViewHolder>() {

    private lateinit var clickListener: OptionClickListener

    inner class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        init {
            itemView.optionButton
                .setOnClickListener { view ->
                    if ((options != null) && (options.size > adapterPosition) && (adapterPosition != -1)) {
                        clickListener.onClick(view, options[adapterPosition])

                    }
                }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val v = LayoutInflater.from(parent?.context).inflate(R.layout.option_button, parent, false)
        return ViewHolder(v)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val option: Option = options[position]
//        val params: LinearLayout.LayoutParams = holder.textViewName.layoutParams as LinearLayout.LayoutParams
        holder.itemView.optionButton.text = option.optionValue
//        holder.itemView.optionButton.setOnClickListener(){
//            clickListener.onClick(holder.itemView.optionButton, options[position])
//        }

    }

    override fun getItemCount(): Int {
        return options.size
    }

    fun setClickListener(itemClickListener: OptionClickListener) {
        this.clickListener = itemClickListener
    }

    interface OptionClickListener {
        fun onClick(view: View, option: Option)
    }
}