# Pepper Potts

### Stats-tv til POMPdeLUX

Læs evt. [dokumentationen](http://shopify.github.io/dashing/) for dashing. 

**Virker kun med Ruby 1.9+**

Installation:

```sh
$> sudo gem install dashing
$> cd /sti/til/hvor-end
$> git clone git@github.com:pompdelux/pepper-potts.git
$> cd pepper-potts
```

* Få certifikat til google analytics, og gem det i `certs`
* Kopier `config.yml.dist` til `config.yml`
* Ret filen til så den passer.

```sh
$> sudo bundle install
$> dashing start
```
Peg din browser på `http://dashing.host:3030/` - done.