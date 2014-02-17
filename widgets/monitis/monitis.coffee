class Dashing.Monitis extends Dashing.Widget
  @accessor 'class', Dashing.AnimatedValue
  ready: ->
    if @get('class')
      val = @get('class')

      console.log val

      if val == 'ok'
        $('.widget-monitis').css('background-color', '#96bf48')
      else
        $('.widget-monitis').css('background-color', '#E74C3C')

  onData: (data) ->
    if data.status
      val = data.status

      console.log data

      if val == 'ok'
        $('.widget-monitis').css('background-color', '#96bf48')
      else
        $('.widget-monitis').css('background-color', '#E74C3C')
